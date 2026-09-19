"""Test prediksi via CLI: python ml/predict.py"""
import requests
r = requests.post("http://localhost:8000/predict", json={
    "commodity_id": 1,
    "regency": "Lampung Barat",
    "farmer_count": 78,
    "total_land": 125,
    "avg_land": 1.6,
    "historical_production": 450
})
print(r.json())