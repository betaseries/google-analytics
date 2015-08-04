# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [1.0.0] - 2015-07-23
### BC Break
- Using Universal Analytics `ga()` syntax instead of deprecated older Google Analytics `_gaq` syntax.
- "total" key in ecommerce tracker's addTransaction has been replaced with "revenue" which should contain the total profit ([read more](http://misterphilip.com/universal-analytics/migration/ecommerce))
- "city", "state" and "country" in ecommerce's tracker addTransaction are not used in the new API, they should be removed from `EcommerceTracker::addTrans` calls. 

### Added
- new `Analytics` wrapper that will print the google snippets along with all the trackers's call.

## [0.1] Initial release with _gaq syntax