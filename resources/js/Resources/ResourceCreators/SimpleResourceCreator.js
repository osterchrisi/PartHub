  export { SimpleResourceCreator };
  import { ResourceCreator } from "./ResourceCreator";
  
  class SimpleResourceCreator extends ResourceCreator {
    constructor(options, tableRebuildFunctions = []) {
      super(options, tableRebuildFunctions);
    }
  
  }