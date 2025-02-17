# frontend/Dockerfile
FROM node:22-alpine

WORKDIR /app/frontend

# Install pnpm
RUN npm install -g pnpm@latest-10

# Copy pnpm specific files
COPY pnpm-lock.yaml ./
COPY package.json ./

# Install dependencies using pnpm
RUN pnpm install --frozen-lockfile

COPY . .

# For development
ENV VITE_HOST=0.0.0.0
CMD ["pnpm", "run", "dev", "--", "--host"]

# For production, uncomment these and comment out the development CMD
# RUN pnpm run build
# EXPOSE 80
# CMD ["pnpm", "run", "preview", "--", "--host", "--port", "80"]
