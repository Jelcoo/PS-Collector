#!/bin/ash

if [ -d /app/frontend/node_modules ]; then
    echo "node_modules already exists"
else
    echo "Installing pnpm dependencies"
    pnpm install --frozen-lockfile
fi

if [ -f /app/frontend/.env ]; then
    echo ".env already exists"
else
    echo "Creating .env"
    cp .env.example .env
fi

pnpm run dev
