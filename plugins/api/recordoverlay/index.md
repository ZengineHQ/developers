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

You can jump to a specific tab by passing the <code>tab</code> query string param in a similar way.

{% highlight js %}
$location.search('tab', 'activities');
{% endhighlight %}

The standard tabs available are listed below.

<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>Name</th>
            <th>Tab</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Linked Records</td>
            <td>linked</td>
        </tr>
        <tr>
            <td>Activities</td>
            <td>activities</td>
        </tr>
        <tr>
            <td>Tasks</td>
            <td>tasks</td>
        </tr>
        <tr>
            <td>Events</td>
            <td>events</td>
        </tr>
        <tr>
            <td>Files</td>
            <td>files</td>
        </tr>
    </tbody>
</table>

To open tabs for a specific plugin use the suffix 'plugin.' + plugin namespace.

{% highlight js %}
$location.search('tab', 'plugin.cloudFiles');
{% endhighlight %}

