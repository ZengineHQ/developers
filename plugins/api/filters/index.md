---
layout: plugin-nav-bar
group: api
subgroup: filters
---

# {{site.productName}} Filters

{{site.productName}} provides filters for you to use in your plugin html, as well as inject as dependencies in your plugin js.

# znUserDate

The `znUserDate` filter takes as input a date string and converts it into the user-preferred format. It works best if the input string is in ISO 8601 or 'YYYY-MM-DD' format; other formats can lead to inconsistent results across browsers. This filter should be used anytime you present dates in your plugin.

You can use it in your html, like any other filter.

{% highlight html%}
{% raw %}
<span>{{'2015-04-09' | znUserDate }}</span>
{% endraw %}
{% endhighlight %}

# znNumericValue

The `znNumericValue` filter takes as input a numeric value and a field object and formats it according to the field settings. The object doesn't need to be the full field; it just needs to contain the following properties:

{% highlight js %}

var field = {
    "settings": {
        "properties": {
            "decimal": 7,
            "currency": "USD"
        }
    }
};
{% endhighlight %}

{% highlight html%}
{% raw %}
<span>{{'1234.5678' | znNumericValue:field }}</span>
{% endraw %}
{% endhighlight %}

In example above, the filter would round the number to 2 decimals, and prepend it with a USD symbol. The final outpout would look like this: `$1,234.57`.
