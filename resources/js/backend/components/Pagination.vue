<template>

  <nav class="pagination" v-if="!client">

    <span class="page-stats">{{pagination.from}} - {{pagination.to}} of {{pagination.total}}</span>

    <a v-if="pagination.prevPageUrl" class="button is-small pagination-previous btn btn" @click="$emit('prev');">
      
      <i class="fas fa-angle-double-left"></i> Prev
    
    </a>

    <a class="button is-small pagination-previous disabled_button" v-else :disabled="true">
      
      <i class="fas fa-angle-double-left"></i> Prev
    
    </a>

    <a v-if="pagination.nextPageUrl" class="button is-small pagination-next" @click="$emit('next');" >
      
      Next <i class="fas fa-angle-double-right"></i>
    
    </a>

    <a class="button is-small pagination-next disabled_button" v-else :disabled="true">
      
      Next <i class="fas fa-angle-double-right"></i>
    
    </a>
  
  </nav>

  <nav class="pagination" v-else>
    
    <span class="page-stats pull-left">
      
      Showing {{ pagination.from }} to {{ pagination.to }} of {{filtered.length}} entries.
      
      <span v-if="filtered.length < pagination.total"
      > (filtered from {{ pagination.total }} total entries) </span>
    </span>

    <el-button-group style="margin-top:-1px;">

      <el-button type="text" icon="el-icon-d-arrow-left" class="mr-1" v-if="pagination.prevPage" @click="$emit('prev');" >Prev</el-button>
      
      <el-button type="text" icon="el-icon-d-arrow-left" class="mr-1" v-else :disabled="true">Prev</el-button>

      <el-button type="text" v-if="pagination.nextPage" @click="$emit('next');">
        
        Next  <i class="el-icon-d-arrow-right"></i>
      
      </el-button>
      
      <el-button type="text" v-else :disabled="true">
        
        Next <i class="el-icon-d-arrow-right"></i>
      
      </el-button>
    
    </el-button-group>
  
  </nav>

</template>

<script>

export default {

  props: [ "pagination", "client", "filtered" ]

};

</script>

<style lang="scss">

.pagination {

  justify-content: flex-end !important;

  .page-stats {

    align-items: center;

    margin-right: 5px;

  }

  i {

    color: #3273dc !important;

  }

}

.page-stats {

  padding: 3px;

  margin-top: 5px;

}

.pagination-previous,

.pagination-next {

  padding: 5px !important;

  cursor: pointer;

  color: #20a8d8 !important;

  border: 1px solid #cacaca;

  border-radius: 3px;

  margin-left: 2px;

}

.disabled_button {

  color: #cacaca !important;

  .fa {

    color: #cacaca !important;

  }

}

</style>
