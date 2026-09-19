"""
FastAPI ML service untuk prediksi panen.
Jalankan: uvicorn ml.app:app --host 0.0.0.0 --port 8000
"""
from fastapi import FastAPI
from pydantic import BaseModel
from pathlib import Path
import joblib
import numpy as np
from datetime import datetime, timedelta

app = FastAPI(title="Lampung Agri ML Service", version="1.0")

MODEL_FILE = Path(__file__).parent / "models" / "harvest_model.pkl"
_bundle = None


def load_model():
    global _bundle
    if _bundle is None and MODEL_FILE.exists():
        _bundle = joblib.load(MODEL_FILE)
    return _bundle


class PredictInput(BaseModel):
    commodity_id: int
    regency: str
    farmer_count: int = 0
    total_land: float = 0.0
    avg_land: float = 0.0
    historical_production: float = 0.0


@app.get("/health")
def health():
    return {"status": "ok", "model_loaded": MODEL_FILE.exists()}


@app.post("/predict")
def predict(payload: PredictInput):
    bundle = load_model()
    today = datetime.now()

    # Heuristic fallback dengan confidence
    base_confidence = 0.75 if bundle else 0.65

    if bundle:
        model = bundle['model']
        le = bundle['le_regency']
        try:
            reg_enc = le.transform([payload.regency])[0]
        except Exception:
            reg_enc = 0

        features = np.array([[
            payload.commodity_id,
            payload.avg_land or 1.0,
            240,  # growth_days typical
            today.month,
            (today + timedelta(days=30)).month,
            reg_enc
        ]])
        predicted = float(model.predict(features)[0])
        # Blend with aggregate
        predicted = 0.6 * predicted * max(payload.farmer_count, 1) + 0.4 * payload.historical_production
        confidence = base_confidence
    else:
        predicted = payload.historical_production or (payload.total_land * 2.5)
        confidence = 0.60

    return {
        "predicted_harvest_date": (today + timedelta(days=30)).strftime("%Y-%m-%d"),
        "predicted_production_ton": round(predicted, 2),
        "confidence_score": confidence,
        "model_version": "xgboost-v1" if bundle else "heuristic-v1"
    }


@app.get("/")
def root():
    return {"service": "Lampung Agri ML", "docs": "/docs"}