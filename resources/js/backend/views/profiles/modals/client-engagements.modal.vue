<template>
  <div>
    <b-modal
      id="client-engagements-modal"
      @hide="hide"
      hide-footer
      size="md"
      :title-html="
        $t('label.add_client_engagements') +
          '<h6>' +
          parent.form.profile_name +
          '</h6>'
      "
    >
      <ul class="nav nav-tabs" id="profileFormTab" role="tablist">
        <li class="nav-item">
          <a
            class="nav-link active"
            id="new_client_engagements-tab"
            data-toggle="tab"
            href="#new_client_engagements_pane"
            role="tab"
            aria-controls="new_client_engagements_pane"
            aria-selected="true"
            >{{ $t("label.new") }}</a
          >
        </li>
        <li class="nav-item">
          <a
            class="nav-link"
            id="existing-client-engagements-tab"
            data-toggle="tab"
            href="#existing_client_engagements_pane"
            role="tab"
            aria-controls="existing_client_engagements_pane"
            aria-selected="false"
            >{{ $t("label.existing_client_engagements") }}</a
          >
        </li>
      </ul>

      <form
        class="form"
        @submit.prevent="onSave"
        @keyup="parent.form.errors.clear($event.target.name)"
      >
        <div class="tab-content" id="profileFormTabContent">
          <div
            class="tab-pane fade show active"
            id="new_client_engagements_pane"
            role="tabpanel"
            aria-labelledby="new-client-engagements"
          >
            <clientEngagementsDiv
              v-for="(engagement, index) in new_client_engagements"
              v-bind:key="'engagement_' + index"
              :root_parent="parent"
              :parent="vm"
              :index="engagement.index"
              :is_deletable="true"
              ref="client-engagements"
            ></clientEngagementsDiv>

            <div class="row">
              <div class="col-md-12">
                <button
                  type="button"
                  class="form-control btn btn-success"
                  @click.prevent="parent.addClientEngagement()"
                >
                  <i class="fas fa fa-plus"></i>
                  {{ $t("label.add_client_engagement") }}
                </button>
              </div>
            </div>
          </div>
          <div
            class="tab-pane fade"
            id="existing_client_engagements_pane"
            role="tabpanel"
            aria-labelledby="existing-client-engagements"
          >
            <clientEngagementsDisplay
              v-for="(engagement, index) in old_client_engagements"
              v-bind:key="'engagement_' + index"
              :display_array="parent.form.client_engagements"
              :root_parent="parent"
              :parent="vm"
              :index="engagement.index"
              :is_editable="false"
              ref="client-engagements-display"
            ></clientEngagementsDisplay>
          </div>
          <!--End Tab Content-->
        </div>

        <div class="form-group">
          <span class="float-right">
            <b-button
              type="submit"
              :variant="parent.form.action == 'Edit' ? 'primary' : 'success'"
              :disabled="parent.btnloading"
              class="mb-2 mt-2 text-capitalize mt-1"
            >
              <span v-if="!parent.btnloading">
                <i class="fas fa-save"></i>
                {{
                  parent.form.action == "Add"
                    ? $t("button.save")
                    : $t("button.save_change")
                }}
              </span>
              <b-spinner v-else small label="Small Spinner"></b-spinner>
            </b-button>
            <b-button
              variant="secondary"
              size="sm"
              @click="$bvModal.hide('client-engagements-modal')"
              >{{ $t("cancel") }}</b-button
            >
          </span>
        </div>
      </form>
    </b-modal>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import clientEngagementsDiv from "./../components/client_engagement_div.vue";
import clientEngagementsDisplay from "./../components/client_engagement_display.vue";

export default {
  name: "client_engagements_modal",
  props: ["parent"],

  data() {
    return {
      loading: true,
      vm: this,
    };
  },

  components: {
    clientEngagementsDiv,
    clientEngagementsDisplay,
  },

  computed: {
    ...mapGetters("profile", {
      responseStatus: "get_response_status",
    }),

    old_client_engagements() {
      let filtered = [];
      this.parent.form.client_engagements.forEach((data, index) => {
        data.index = index;
        if (data.id != undefined) filtered.push(data);
      });

      return filtered;
    },

    new_client_engagements() {
      let filtered = [];
      this.parent.form.client_engagements.forEach((data, index) => {
        data.index = index;
        if (data.id == undefined) filtered.push(data);
      });

      return filtered;
    },
  },

  methods: {
    ...mapActions("profile", ["post_client_engagement"]),

    hide() {
      this.$emit("hide");
    },

    onSave() {
      this.parent.btnloading = true;

      let params = {
        api_token: this.$user.api_token,
        action: "Add",
        action_route: "/add_client_engagements",
        profile_id: this.parent.form.profile_id,
        client_engagements: this.new_client_engagements,
      };

      this.post_client_engagement(params)
        .then((resp) => {
          this.parent.btnloading = false;
          this.$bvModal.hide("client-engagements-modal");
          if (this.responseStatus) {
            //Add callback function here if necessary
            this.parent.addedNewClientEngagementSuccessfully();

            let message = {
              create: this.$t("new_record_created"),
              update:
                this.$t("updated_message1") +
                this.$t("label.contact_type") +
                ` ID: ${this.parent.form.profile_id}` +
                this.$t("updated_message2"),
              title: {
                create: this.$t("new_record_created"),
                update: this.$t("record_updated"),
              },
            };

            this.parent.makeToast(
              "success",
              message.title.create,
              message.create
            );
          }
        })
        .catch((error) => {
          console.log(error);
          this.parent.form.errors.record(error.response.data.errors);
          this.parent.btnloading = false;
        });
    },
  },
};
</script>
