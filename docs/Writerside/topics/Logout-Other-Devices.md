# Logout Other Devices

Once the user logout other devices, a websocket event will trigger.

## Channel
The event payload will be sent to this private channel `session.logout.{userId}` with a broadcast name of `.session.logout.other.device`

## Authorization
This channel is authenticated and only the user which have the exact User ID listener can receive the event payload.

## Event Payload
This is an example event payload structure
```json
{
    "message": "Session Logout Other Devices",
    "session_check": 'http://......./sessions/health'
    
}
```

> `session_check` is the `API` route that the frontend need to request if the current session with the backend is still healthy, and it has not been logout from other device.
