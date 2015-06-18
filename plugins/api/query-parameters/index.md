---
layout: plugin-nav-bar
group: api
subgroup: query-parameters
---

# {{site.productName}} Query Parameters

There are several query string parameters that can be passed to any route in the app, and will display something
over the underlying page.

You can set these query parameters using the Angular <a target="_blank" href="{{site.angularDomain}}/{{site.angularVersion}}/docs/api/ng/service/$location"><code>$location</code></a> service. Make sure to include the `$location` service dependency, which you can use in code similar to the following example.

{% highlight js%}
formId = 1;
recordId = 2;
$location.search('record', formId + '.' + recordId);
{% endhighlight %}

# Record Overlay {#record}

Plugins can open the record overlay by passing the appropriate form ID and record ID to the `record` query string param. The format of the `record` query param is `{formId}.{recordId}`. 

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

# 

To open tabs for a specific plugin use the prefix 'plugin.' + plugin namespace.

{% highlight js %}
$location.search('tab', 'plugin.cloudFiles');
{% endhighlight %}

# File Viewer Modal {#file-viewer}

Plugins can also open a file viewer by passing the file ID to the `file-viewer` query string param. Currently, this is only supported for document type files (i.e. with extensions like .pdf, .doc, and .docx).

{% highlight js%}
fileId = 2;
$location.search('file-viewer', fileId);
{% endhighlight %}


# Image Lightbox {#lightbox}

Plugins can open images in a lightbox by passing the appropriate resource name and resource ID to the `lightbox` query string param. The format of the `lightbox` query param is `{resourceName}.{resourceId}`.

{% highlight js%}
resourceName = 'file';
resourceId = 2;
$location.search('lightbox', resourceName + '.' + resourceId);
{% endhighlight %}
