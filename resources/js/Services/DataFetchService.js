export { DataFetchService}

class DataFetchService {
    static fetch(url) {
      return $.ajax({
        url,
        dataType: 'json',
        error: function (error) {
          console.error(error);
        }
      });
    }
  
    static getSuppliers() {
      return DataFetchService.fetch('/suppliers.get');
    }
  
    static getCategories() {
      return DataFetchService.fetch('/categories.get');
    }
  
    static getFootprints() {
      return DataFetchService.fetch('/footprints.get');
    }
  
    static getLocations() {
      return DataFetchService.fetch('/locations.get');
    }
  }