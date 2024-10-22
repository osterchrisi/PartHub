export { CategoryCreator };
import { ResourceCreator } from "./ResourceCreator";

class CategoryCreator extends ResourceCreator {
    constructor(options, tableRebuildFunctions = []) {
      super(options, tableRebuildFunctions);
    }
  
  }  