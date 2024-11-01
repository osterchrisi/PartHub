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
      return this.fetch('/suppliers.get');
    }
  
    static getCategories() {
      return this.fetch('/categories.get');
    }
  
    static getFootprints() {
      return this.fetch('/footprints.get');
    }
  
    static getLocations() {
      return this.fetch('/locations.get');
    }
  }