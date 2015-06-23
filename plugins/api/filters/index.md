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
