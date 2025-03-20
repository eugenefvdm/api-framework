# API Collection

Another day, another API.

A small and compact code base that only has the essential API calls that I need.

List of APIs:

- BulkSMS
- Hello Peter
- Slack
- Telegram
- ZADomains

## Usage

See `index.php` for usage examples.

One way to get up and running is turning on or off the API you want to use. For example, below I'm keen to test the ZADomains API, so I set it's value to `true`.

```env
ENABLE_BULKSMS=false
ENABLE_DISCORD=false
ENABLE_HELLO_PETER=false
ENABLE_SLACK=false
ENABLE_TELEGRAM=false
ENABLE_ZADOMAINS=true
```
