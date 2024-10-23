export { CategoryCreator };
import { ResourceCreator } from "./ResourceCreator";

class CategoryCreator extends ResourceCreator {
    constructor(options, tableRebuildFunctions = []) {
        this.categoryId = options.categoryId || null;
        super(options, tableRebuildFunctions);
    }

    collectFormData (){
        super.collectFormData;
        if (this.categoryId) {
            this.data['parent_category'] = this.categoryId;
          }
    }
}  