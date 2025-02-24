#!/bin/ash

pnpm install --frozen-lockfile

if [ -f /app/frontend/.env ]; then
    echo ".env already exists"
else
    echo "Creating .env"
    cp .env.example .env
fi

pnpm run dev
