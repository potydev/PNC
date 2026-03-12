FROM golang:1.25-alpine AS builder
WORKDIR /app

COPY go.mod go.sum* ./
RUN go mod download

COPY . .
RUN CGO_ENABLED=0 GOOS=linux GOARCH=amd64 go build -o /bin/sso ./cmd/sso

FROM alpine:3.20
WORKDIR /app
RUN adduser -D -H -u 10001 sso

COPY --from=builder /bin/sso /usr/local/bin/sso
COPY .env.example /app/.env.example

USER sso
EXPOSE 8088
CMD ["/usr/local/bin/sso"]
