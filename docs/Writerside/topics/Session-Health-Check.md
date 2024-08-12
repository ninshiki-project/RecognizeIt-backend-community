# Session Health Check

This will send every 3 minutes for session heartbeat check.

## Channel
The event payload will be sent to this private channel `session.health.check` with a broadcast name of `.session.heartbeat.check`

## Authorization
This channel is public and all authenticated listener can receive the event payload.

## Event Payload
This is an example event payload structure
```json
{
    "message": "Session Health Check",
    "session_check": "http://......./sessions/health"
}
```

> This broadcast will serve for your frontend as a counter to when the time to check if the user token is still valid or not using the `session_check` response URI.
