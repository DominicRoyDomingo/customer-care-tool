<template>
  <div class="card mb-0">
    <div
      class="card-header py-3 d-flex flex-row align-items-center justify-content-between"
    >
      <h6 class="m-0 font-weight-bold text-primary">
        {{ $t("course_details") }}
      </h6>
      <div class="dropdown no-arrow">
        <!-- <a
          href="javascript:;"
          @click="on_get_terms"
          v-b-tooltip.hover
          :title="`Link to ${$t('label.terminilogies')}`"
          class="h5 float-right mb-0"
        >
          <i class="fas fa-link"></i>
        </a> -->
      </div>
    </div>

    <b-overlay
      id="overlay-background"
      :show="isLinked"
      :variant="'light'"
      opacity="0.85"
      :blur="'1px'"
      rounded="sm"
    >
      <div class="card-body">
        <div v-if="isLoading">
          <b-spinner small label="Spinning"></b-spinner>
        </div>
        <div class="row" v-else>
          <div class="col-md-12 p-0">
            <table class="table mb-0 table-striped">
              <tbody>
                <tr>
                  <th style="width: 40%">
                    {{ $t("course_type_title") }}
                  </th>
                  <td>
                    <span class="mr-1" v-html="on_display_course_name" />
                  </td>
                </tr>
                <tr>
                  <th>
                    {{ $t("number_credits") }}
                  </th>
                  <td>{{ course_info.number_credit }}</td>
                </tr>
                <tr>
                  <th>
                    {{ $t("time_duration") }}
                  </th>
                  <td>{{ course_info.time_duration }}</td>
                </tr>
                <tr>
                  <th>
                    <span class="text-capitalize">
                      {{ $t("price") }}
                      <small>
                        (<i class="fas fa-euro-sign text-info"></i>)
                      </small>
                    </span>
                  </th>
                  <td>{{ course_info.price | money }}</td>
                </tr>
                <tr>
                  <th>
                    {{ $t("label.address") }}
                  </th>
                  <td>{{ course_info.address }}</td>
                </tr>
                <tr>
                  <th>
                    {{ $t("label.language") }}
                  </th>
                  <td>{{ course_info.language }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </b-overlay>
  </div>
</template>

<script>
export default {
  props: {
    course_info: {
      type: Object,
    },
  },
  data() {
    return {
      isLoading: false,
      isLinked: false,
    };
  },
  computed: {
    on_display_course_name() {
      let courseTypes = this.course_info.has_course_types;

      let set = "";
      let i = 1;
      courseTypes.forEach((ele) => {
        set += ele.course_type_name;
        if (i < courseTypes.length) {
          set += ", ";
        }
        i++;
      });
      return set;
    },
  },
  methods: {
    load_course() {},
  },
};
</script>

