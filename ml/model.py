"""
Training script untuk model prediksi panen.
Jalankan: python ml/model.py
"""
import sqlite3
import pandas as pd
import numpy as np
import joblib
from pathlib import Path
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import LabelEncoder
from sklearn.metrics import mean_absolute_error, r2_score
from xgboost import XGBRegressor

DB_PATH = Path(__file__).parent.parent / "database" / "lampung_agri.db"
MODEL_PATH = Path(__file__).parent / "models"
MODEL_PATH.mkdir(exist_ok=True)


def load_data():
    conn = sqlite3.connect(DB_PATH)
    df = pd.read_sql_query("""
        SELECT f.id, f.commodity_id, f.regency, f.land_size_ha,
               f.planting_date, f.estimated_harvest_date,
               f.estimated_production_ton, v.variety_name
        FROM farmers f
        LEFT JOIN commodity_varieties v ON f.variety_id = v.id
        WHERE f.estimated_production_ton IS NOT NULL
    """, conn)
    conn.close()
    return df


def build_features(df):
    df = df.copy()
    df['planting_date'] = pd.to_datetime(df['planting_date'], errors='coerce')
    df['estimated_harvest_date'] = pd.to_datetime(df['estimated_harvest_date'], errors='coerce')
    df['growth_days'] = (df['estimated_harvest_date'] - df['planting_date']).dt.days
    df['plant_month'] = df['planting_date'].dt.month.fillna(6)
    df['harvest_month'] = df['estimated_harvest_date'].dt.month.fillna(10)

    # Label encode regency
    le_regency = LabelEncoder()
    df['regency_enc'] = le_regency.fit_transform(df['regency'].fillna('Unknown'))

    return df, le_regency


def train():
    df = load_data()
    if len(df) < 10:
        print("Data terlalu sedikit untuk training. Generate seed data dulu.")
        return

    df, le_regency = build_features(df)

    feature_cols = ['commodity_id', 'land_size_ha', 'growth_days',
                    'plant_month', 'harvest_month', 'regency_enc']
    X = df[feature_cols].fillna(0)
    y = df['estimated_production_ton'].fillna(0)

    X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

    model = XGBRegressor(
        n_estimators=200, max_depth=5, learning_rate=0.08,
        objective='reg:squarederror', random_state=42
    )
    model.fit(X_train, y_train)

    pred = model.predict(X_test)
    print(f"MAE: {mean_absolute_error(y_test, pred):.3f}")
    print(f"R²:  {r2_score(y_test, pred):.3f}")

    joblib.dump({'model': model, 'le_regency': le_regency, 'features': feature_cols}, MODEL_PATH / "harvest_model.pkl")
    print(f"✅ Model saved to {MODEL_PATH / 'harvest_model.pkl'}")


if __name__ == "__main__":
    train()