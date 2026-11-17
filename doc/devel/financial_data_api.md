# Finnhub API

## Register 

### API.Key

- URL: finnhub.io/register
- Name: hahela; E-Mail: hemm@nexgo.de; Password: 3k2eT-9=
- API-Key: dbms9b9r01qn1o673430dbms9b9r01qn1o67343g

### Webhook

- URL:
- Secret: dbms9b9r01qn1o67344g

All requests' header made from our server will contain field 
"X-Finnhub-Secret": "dbms9b9r01qn1o67344g" for authentication. 
To acknowledge receipt of an event, your endpoint must return 
a 2xx HTTP status code. Acknowledge events prior to any logic 
that needs to take place to prevent timeouts. Your endpoint is 
disabled if it fails to acknowledge events over consecutive days.

### Security

- Two-factor Authentication (Authenticator app): Off
- Passkeys: None


## API Documentation

- Base URL: /api/v1
- Swagger schema: Download https://finnhub.io/static/swagger.json

### Authentication

All GET request require a token parameter token=apiKey in the URL or a 
header X-Finnhub-Token : apiKey. 

### Rate Limits

If your limit is exceeded, you will receive a response with status code 429.
On top of all plan's limit, there is a 30 API calls/ second limit.


