# Peer Recognition

Once there is a new post for recognition, a websocket event will trigger.

## Channel
The event payload will be sent to this private channel `server.post.new` with a broadcast name of `new.post`

## Authorization
This channel is public and all authenticated listener can receive the event payload.

## Event Payload 
This is an example event payload structure
```json
{
    "message": "New Post Received!",
    "meta": {
        "post_id": 1,
        "post_by": {
            "id": 31,
            "name": "John Doe",
            "avatar": "https://avatar.iran.liara.run/public/38"
        }
    }   
}
```
