---
layout: nav-bar
group: intro
---
# Introduction

{{site.productName}} provides a [RESTful](https://en.wikipedia.org/wiki/Representational_state_transfer){:target="_blank"} API which maps virtually every [resource]({{site.baseurl}}/rest-api/resources) in the platform to a list of URIs. These URIs can be retrieved and manipulated with [HTTP verbs]({{site.baseurl}}/rest-api/conventions/http-verbs).

Please note that all development is subject to the [API License Agreement]({{site.marketingDomain}}/terms-of-service/api).

## Audience

The API is made available for a wide variety of use cases, **including** commercial applications. Some possibilities include:

* [JavaScript plugins]({{site.baseurl}}/plugins/)
* Mobile clients
* Integration with third party web & mobile applications
* Bulk fetching & processing of private data
* ... and more

The documentation herein is provided as a reference guide for third party developers who wish to learn more about the API's conventions and resources.

## Restrictions

Rate limiting in version 1 of the API is primarily considered on a per-user basis — or more accurately described, per access token. Each access token is allowed **{{site.rateLimit}} queries per {{site.rateLimitWindow}} minute** window. Additionally, these limits apply on a per-plugin basis. So one user can also make {{site.rateLimit}} requests per {{site.rateLimitWindow}} minutes per plugin.

We expect you to be kind - only make requests which are necessary, use caching techniques whenever possible, and use the API in a way that does not have a negative impact on {{site.productName}}'s business or infrastructure. We, of course, reserve the right to suspend API access to anybody acting against the spirit of these restrictions.


The API is currently provided free of charge. We expect for this to remain true indefinitely, as our intention is to charge the end users of {{site.productName}}, not the developers who add value. But again, we reserve the right to reverse this decision in the future.

These restrictions will become more formally defined over time, but the purpose and intended audience of the API is a constant.
