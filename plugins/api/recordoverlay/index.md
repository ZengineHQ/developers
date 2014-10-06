---
layout: plugin-nav-bar
group: api
subgroup: recordoverlay
---

# Record Overlay

Plugins can open the record overlay by passing the appropriate form ID and record ID in the <code>record</code> query string param. The format of the <code>record</code> query param is <code>{formId}.{recordId}</code>. You can set the query param using the Angular <a target="_blank" href="{{site.angularDomain}}/{{site.angularVersion}}/docs/api/ng/service/$location"><code>$location</code></a> service. Make sure to include the <code>$location</code> service dependency, then you can use code similar to the following example.

{% highlight js%}
formId = 1;
recordId = 2;
$location.search('record', formId + '.' + recordId);
{% endhighlight %}

