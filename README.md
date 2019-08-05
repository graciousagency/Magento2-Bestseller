This is a module that extends Magento2 API to get list of TOP products:
 - TOP selling products
 - TOP Free products
 - TOP Rated products
 
Top selling and free products are fetched from Magento2 reports.
Top rated products are fetched from Rates module and results are based on aggregated data.

## API requests
```GET /V1/products/top/{type}``` - Get list of top products by type.
Where ```type``` can be:
- **selling** - TOP selling products
- **free** - TOP Free products
- **rated** - TOP Rated products

###### Search criteria params
**pageSize** - Page size

**currentPage** - Current page

**ratingCode** - filter by rating type. This options is related ony for ```rated``` type. Possible values can be found in ```rating``` table.

**period** - filter by period. This options is related only for ```selling``` or ```free``` type. Possible values are:

- yearly - annual report (default)
- monthly - monthly report
- daily - daily report

**filter_groups** - As product search criteria this filter will allow you to process products collection with more requirements.

###### Search criteria examples:
Filter top selling products by:
- period: daily
- price > 10
- visibility = 4
- pageSize = 10
- currentPage = 2

```bash
$ curl -X GET \
  'https://example.com/rest/all/V1/products/top/selling?searchCriteria%5Bfilter_groups%5D%5B0%5D%5Bfilters%5D%5B0%5D%5Bfield%5D=visibility&searchCriteria%5Bfilter_groups%5D%5B0%5D%5Bfilters%5D%5B0%5D%5Bvalue%5D=4&searchCriteria%5Bfilter_groups%5D%5B0%5D%5Bfilters%5D%5B0%5D%5Bcondition_type%5D=eq&searchCriteria%5BpageSize%5D=10&searchCriteria%5BcurrentPage%5D=2&searchCriteria%5Bfilter_groups%5D%5B0%5D%5Bfilters%5D%5B1%5D%5Bfield%5D=price&searchCriteria%5Bfilter_groups%5D%5B0%5D%5Bfilters%5D%5B1%5D%5Bvalue%5D=10&searchCriteria%5Bfilter_groups%5D%5B0%5D%5Bfilters%5D%5B1%5D%5Bcondition_type%5D=gt&searchCriteria%5Bperiod%5D=daily' \
  -H 'authorization: Bearer {api-token}' \
  -H 'cache-control: no-cache'
```

Filter top rated products by:
- ratingCode: Rating
- status = 2
- visibility = 4
- pageSize = 10
- currentPage = 2

```bash
$ curl -X GET \
  'https://example.com/rest/all/V1/products/top/rated?searchCriteria%5Bfilter_groups%5D%5B0%5D%5Bfilters%5D%5B0%5D%5Bfield%5D=visibility&searchCriteria%5Bfilter_groups%5D%5B0%5D%5Bfilters%5D%5B0%5D%5Bvalue%5D=4&searchCriteria%5Bfilter_groups%5D%5B0%5D%5Bfilters%5D%5B0%5D%5Bcondition_type%5D=eq&searchCriteria%5BpageSize%5D=10&searchCriteria%5BcurrentPage%5D=2&searchCriteria%5Bfilter_groups%5D%5B0%5D%5Bfilters%5D%5B1%5D%5Bfield%5D=status&searchCriteria%5Bfilter_groups%5D%5B0%5D%5Bfilters%5D%5B1%5D%5Bvalue%5D=1&searchCriteria%5Bfilter_groups%5D%5B0%5D%5Bfilters%5D%5B1%5D%5Bcondition_type%5D=eq&searchCriteria%5BratingCode%5D=Rating' \
  -H 'authorization: Bearer {api-token}' \
  -H 'cache-control: no-cache'
```

## Dependencies
This module is using exists functionality of next modules:
- **magento/module-catalog**
- **magento/module-review**
- **magento/module-sales**
