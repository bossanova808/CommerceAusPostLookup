# Commerce Aus Post Lookup

Craft Commerce plugin for Australia Post API lookup of suburbs or postcodes

Makes it easier to set up a field that the user can fill in with either there postcode or suburb details and get back verified data from Australia Post.

## Requirements & Important Notes!

* **Requires:** jquery autocomplete - https://github.com/devbridge/jQuery-Autocomplete
* **Requires:** curl

**Currently uses hardcoded values for the states names so you MUST set up your states to match mine** - I will possible change this to either look up values in your database (or accept a pull request!), but I'm holding off on this for now as I presume Commerce will actually auto-populate this data on release.

The order must match this (check your market_states table):

```
        $statesNameMap = array(
            "VIC"  => "Victoria",
            "SA" => "South Australia",
            "WA" => "Western Australia",
            "NT" => "Northern Territory",
            "NSW" => "New South Wales",
            "QLD" => "Queensland",
            "TAS" => "Tasmania",
        );


        $statesIDMap = array(
            "VIC"  => 1,
            "SA" => 2,
            "WA" => 3,
            "NT" => 4,
            "NSW" => 5,
            "QLD" => 6,
            "TAS" => 7,
        );


```

## Install

* Download the latest release from the releases tab
* Upload the commerceauspostlookup/ folder to your craft/plugins/ folder.
* Go to Settings > Plugins from your Craft control panel and enable the Commerce Aus Post Lookup plugin.
* Click on “Aus Post Lookup for Commerce” to go to the plugin’s settings page, and configure the plugin.

## Settings

### Debug

Just logs a bit of info to the plugin log.

### API Key

You can use the testing API key provided on the settings page to get going, just cut and paste it in.  However for deployment you must request your own API key from the Australia Post Developer Centre at https://developers.auspost.com.au/

### URL Prefix

The domestic service API is at auspost.com.au so put this in.  I've just made it configurable in case I add other things to this later.

## Styling the results

When working, it will look something like this:

Search by Suburb:

![Alt text](/screenshots/suburb.png?raw=true "Search by suburb")

Search by postcode:

![Alt text](/screenshots/postcode.png?raw=true "Search by postcode")

Search with no results:

![Alt text](/screenshots/noresults.png?raw=true "No results")

Something like this will make it look like the above - see https://github.com/devbridge/jQuery-Autocomplete for docs.

```
.autocomplete-suggestions { 
    border: 1px solid #999; 
    background: #FFF; 
    overflow: auto; 
    width:auto; 
}

.autocomplete-suggestion { 
    padding: 2px 5px; 
    white-space: nowrap; 
    overflow: hidden; 
}

.autocomplete-selected { 
    background: @color-soft-grey; 
}

.autocomplete-suggestions strong { 
    font-weight: normal; 
    color: @color-blue-foreground; 
}

.autocomplete-group { 
    padding: 2px 5px; 
}

.autocomplete-group strong { 
    display: block; 
    border-bottom: 
    1px solid #000; 
}
```

## Example Template to get it working

I suggest you create a testing template somewhere you can use to set up your first field and later on to check in case of connectivity issues etc.  That said, I've never actually caught the API being down, so reliability is very good.


```
{% extends "_layouts/layout_page" %}
{%import "_macros/macros" as macros %}

{% set page_h1="Test Commerce Aus Post Code" %}

{% block structure %}

    {% embed "_layouts/skeleton_one_column" %}
                    
        {% block leftColumn %}

            <p>Sends a test query of postcode 3000 to Commerce Aus Post Code Lookup & Shows the returned values to check everything is working...</p>
            <p>Enter the data (completion starts at 4 characters) - and choose a matching option...</p>
            <form method="POST" id="testPCForm">
                {{ getCsrfInput() }}
                <input type="hidden" name="action" value="ausPostLookup/lookup">
                
                Enter Postcode or Suburb : <input type="text" id="postcode" size="40" name="query" value="">

                {{ macros.space (3) }}

                <div id="results">
                    <h3> Selected data: </h3>
                    Postcode: <span id="pc"></span> <br>
                    Suburb: <span id="suburb"></span> <br>
                    State ID: <span id="stateId"></span> <br>
                    State Name: <span id="stateName"></span> <br>
                </div>

            {{ macros.space(10)}}

        {% endblock leftColumn %}

    {% endembed %}

{% endblock structure %}

{% set js %}

    //clear the postcode autocomplete if field clicked in...
    $('#postcode').on('click', function() {
        this.value = '';
    });

    optionsPostcode = { serviceUrl:'/actions/commerceAusPostLookup/lookup', 
                        minChars:'4',
                        type:'POST',
                        preventBadQueries:'true',
                        showNoSuggestionNotice:'true',
                        noSuggestionNotice:"Can't find a matching postcode or suburb with Australia Post.",
                        onSelect: function (suggestion) {
                                $( "#pc" ).html(suggestion.data['postcode']);
                                $( "#suburb" ).html(suggestion.data['suburb']);
                                $( "#stateId" ).html(suggestion.data['stateId']);
                                $( "#stateName" ).html(suggestion.data['stateName']);
                            }
                        };

    a = $('#postcode').autocomplete(optionsPostcode);


{% endset %}

{% includeJsFile "/js/jquery.autocomplete.min.js" %}
{% includeJs js %}

```