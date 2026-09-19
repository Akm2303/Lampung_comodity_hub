#!/bin/bash
set -e

echo "🌾 Lampung Agri Hub — Setup"
echo "=============================="

# 1. Init SQLite DB
echo "→ Creating SQLite database..."
mkdir -p database
rm -f database/lampung_agri.db
sqlite3 database/lampung_agri.db < database/schema.sql
sqlite3 database/lampung_agri.db < database/seed.sql
echo "  ✓ Schema + seed loaded"

# 2. Generate 100 farmers
echo "→ Generating 100 farmers..."
php database/generate_seed.php 2>/dev/null || echo "  ⚠ PHP CLI needed for farmer generation"

# 3. Build & run Docker
echo "→ Building Docker containers..."
docker compose build
docker compose up -d

# 4. Train ML model
echo "→ Training ML model..."
docker compose --profile train run --rm ml-trainer

echo ""
echo "✅ Setup complete!"
echo "   Web:  http://localhost:8080"
echo "   ML:   http://localhost:8000/docs"
echo ""
echo "Login: superadmin / password"