## Forecast

Adds cachable weather forecast tags using data from Dark Sky.

This addon does not dictate or include a template, it makes the weather data accessible in any template on your site. You're free to do any layout you wish.


## Prerequisits

You need an [API key from Dark Sky](https://darksky.net) to be able to use this addon. API keys are free for up to 1000 requests/day.

It is advised to [read up on the Dark Sky API](https://darksky.net/dev/docs) to better grok the terms used below and why it's such a good fit for Statamic.

## Installation

* Clone this repository into `site/addons`, or use as a git submodule (recommended).
* Set api_key in `site/settings/addons/forecast.yaml`:

        api_key: YOUR_API_KEY_HERE


## Configuration

The addon can be configured using the following keys in `site/settings/addons/forecast.yaml`, or using the control panel.

    api_key: YOUR_API_KEY_HERE
    cache_mins: 10800
    block: daily
    lang: en
    units: si

## Usage

Use the `{{ forecast }}` tag to retrieve a forecast and use the variables from the Dark Sky JSON response.

    {{ forecast lat="12" lng="52" }}
        <p>Summary for the week: {{ summary }}</p>
        <ul>
            {{ data }}
                <li>{{ time format="Y-m-d" }}: {{ summary }}</li>
            {{ /data }}
        </ul>
    {{ /forecast }}

Tip: Use the `icon` variable as image src.


## License

Forecast is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
