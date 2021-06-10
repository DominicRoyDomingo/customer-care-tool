<template>

  <table :class="tableClass">

    <thead>

      <tr :class="headerClass">

        <th

          v-for="column in columns"

          :key="column.name"

          @click="$emit('sort', column.name)"

          :class="sortKey === column.name ? (sortOrders[column.name] > 0 ? 'sorting_asc' : 'sorting_desc') : 'sorting'"

          :style="'width:'+column.width+';'+'cursor:pointer;text-align:'+column.align"

        >{{ $t(column.label)}}</th>

      </tr>

    </thead>

    <slot></slot>

  </table>

</template>

<script>

export default {

  props: [ "columns", "sortKey", "sortOrders", "tableClass", "headerClass" ]

};

</script>

<style lang="scss" scoped>

.tableFilters {

  margin-bottom: 5px;

}

.tableFilters {

  margin-bottom: 10px;

  input {

    width: 175px;

  }

  .control {

    float: right;

  }

}

.table {

  width: 100%;
  
  border: 1px solid #ebeef0 !important;
        

  .sorting {

    background-image: url("/images/vendor/datatables/media/sort_both.png");

    background-repeat: no-repeat;

    background-position: center right;
  }

  .sorting_asc {

    background-image: url("/images/vendor/datatables/media/sort_asc.png");

    background-repeat: no-repeat;

    background-position: center right;

  }

  .sorting_desc {

    background-image: url("/images/vendor/datatables/media/sort_desc.png");

    background-repeat: no-repeat;

    background-position: center right;

  }

}

.table-striped tbody tr:nth-of-type(odd) {
    background-color: rgba(0, 0, 0, 0.05);
}

.table-active th{

  background: #ebeef0;

  border: 1px solid #ebeef0;

}

.table thead th{

  border-bottom: 0 !important;

}
.table th, .table td {

  border-top: 1px solid #ebeef0 !important;

}

.pagination {

  margin: 10px;

}

.projects {

  border: 1px solid #ebeef0;

  border-radius: 3px;

}

th:nth-last-child(1) {

  text-align: center;

}

</style>