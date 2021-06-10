<template>
  <b-modal
    id="information-list-modal"
    hide-footer
    hide-header
    size="lg"
    no-close-on-backdrop
  >
    <v-app id="create__container">
			<v-card elevation="0">
				<v-form ref="form">
					<v-card-title class="title blue-grey lighten-4 text--secondary">
						<span>
							{{ $t("information_type_edit_list_button") }}
							<small>({{ parent.actorInfo.fullname }})</small>
						</span>
						<v-spacer></v-spacer>
						<v-btn
							icon
							color="error lighten-2"
							@click="modalClose"
						>
							<v-icon>mdi-close</v-icon>
						</v-btn>
					</v-card-title>
					<v-card-text class="modal__content">
						<v-card class="border-0" elevation="0">
							<v-container>
								<div class="note-div note-data" 
										v-for="(other_info, index) in parent.form.other_infos"
										v-bind:key="'other_info_' + index">
									<div class="row no-gutters">
										<div class="col-md-11 text-left">
											<hr />
										</div>
										<div class="col-md-1 text-right">
											<v-btn fab dark small color="error" @click.prevent="deleteOtherInfoDiv(index)">
												<v-icon dark>mdi-close</v-icon>
											</v-btn>
										</div>
									</div>

									<div class="form-group">
										<label for="information_type">{{ $t("label.information_type") }}</label>
											<v-select
													name="name"
													label="base_name"
													v-model="parent.form.other_infos[index].type"
													:options="parent.newInformationTypes"
													id="information_type"
											>
													<template #list-header>
															<li style="margin-left:20px;" class="mb-2">
																	<b-link href="#" @click="openInformationTypeModal">
																			<i class="fa fa-plus" aria-hidden="true"></i>
																			{{ $t("information_type_new_button") }}
																	</b-link>
															</li>
													</template>

													<template #option="{ information_type_name }">
														<span v-html="information_type_name" />
													</template>
											</v-select>
									</div>

									<div class="form-group" v-if="parent.form.other_infos[index].type != null">
										<div v-if="parent.form.other_infos[index].type.type_of_data == 'Short Text'">
											<v-text-field
												v-model="parent.form.other_infos[index].value"
												:rules="[parent.rules.required]"
												:label="$t('short_text')"
											></v-text-field>
										</div>

										<div v-if="parent.form.other_infos[index].type.type_of_data == 'Email Format'">
											<v-text-field
												v-model="parent.form.other_infos[index].value"
												:rules="[parent.rules.required, parent.rules.email]"
												:label="$t('label.email')"
											></v-text-field>
										</div>

										<div v-if="parent.form.other_infos[index].type.type_of_data == 'Numeric'">
											<v-text-field
												v-model="parent.form.other_infos[index].value"
												:rules="[parent.rules.required, parent.rules.number]"
												single-line
												type="number"
												:label="$t('numeric')"
											></v-text-field>
										</div>

										<div v-if="parent.form.other_infos[index].type.type_of_data == 'Date'">
											<v-menu content-class="elevation-0" >
												<template v-slot:activator="{on}">
													<v-text-field
														v-model="parent.form.other_infos[index].value"
														v-on="on"
														:label="$t('date')"
														:rules="[parent.rules.required]"
													></v-text-field>
												</template>
												<v-date-picker v-model="parent.form.other_infos[index].value" elevation="15"></v-date-picker>
											</v-menu>
										</div>

										<div v-if="parent.form.other_infos[index].type.type_of_data == 'Long Text'">
											<v-textarea
												:rules="[parent.rules.required]"
												v-model="parent.form.other_infos[index].value"
												:label="$t('long_text')"
											></v-textarea>
										</div>
									</div>
								</div>
								<!-- <otherInfoDiv
										v-for="(other_info, index) in parent.new_other_infos"
										v-bind:key="'other_info_' + index"
										:root_parent="parent"
										:parent="vm"
										:index="other_info.index"
										:is_deletable="true"
										ref="otherInfoDiv"
								></otherInfoDiv> -->
								<div class="row">
										<div class="col-md-12">
										<v-btn
												tile
												block
												color="success lighten-1"
												@click.prevent="parent.addOtherInfoDiv"
										>
												<v-icon>mdi-sticker-plus</v-icon>&nbsp;
												{{ $t("label.add_other_info") }}
										</v-btn>
										</div>
								</div>
							</v-container>
						</v-card>
					</v-card-text>
					<v-divider style="margin-bottom: 0"></v-divider>
            <v-card-actions class="modal__footer blue-grey lighten-5">
              <v-spacer></v-spacer>
              <v-btn color="error" text tile @click="modalClose">
                {{ $t("cancel") }}
              </v-btn>
              <v-btn color="success" tile @click="onSubmit">
                <div v-if="isUpdating" class="text-center">
                  <v-progress-circular
                    size="20"
                    width="1"
                    color="white"
                    indeterminate
                  >
                  </v-progress-circular>
                </div>
                <div v-else>
                  <div>
                    {{ $t('button.save_change') }}
                  </div>
                </div>
              </v-btn>
            </v-card-actions>
				</v-form>
			</v-card>
		</v-app>
  </b-modal>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

// Snippets
import TermDescriptionIndex from "../../medical_stuff/snippets/term_descriptions/index";

// Modals
import CreateTermDescription from "../../medical_stuff/modals/create-termDescription.modal";

import medicalMixin from "../../medical_stuff/mixins/medicalMixin";
import termMixin from "../../medical_stuff/mixins/termMixin";

export default {
  props: ["parent"],

  data() {
    return {
			isUpdating: false,
    };
  },

  methods: {
		...mapActions("actor", ["update_information_list", "update_actor"]),
    openInformationTypeModal() {
      this.parent.$bvModal.hide(this.parent.modalId);

      this.parent.$bvModal.show("information-type-add-modal");
    },

		modalClose(done) {
      this.$bvModal.hide("information-list-modal");
    },

		onSubmit() {
			this.isUpdating = true;
      let otherInfos = [];

      this.parent.form.other_infos.forEach(function(data) {
        if(data.type == "" || data.value == "") return;
        otherInfos.push({
          index: data.index,
          type: data.type.id,
          value: data.value,
        })
      });

      if(otherInfos.length == 0) otherInfos = ""
      
      let params = {
        api_token: this.$user.api_token,
        id: this.parent.actorInfo.id,
        locale: this.$i18n.locale,
        lang: this.parent.form.language,
        other_info: otherInfos,
      };
      this.update_information_list(params)
        .then((resp) => {
          this.isUpdating = false;
          this.$bvModal.hide("information-list-modal");
          if (this.parent.actorsResponseStatus) {
            let response = this.parent.actorsResponseStatus;
            response.index = this.parent.editingIndex;
            this.update_actor(response);

            let message = {
              update:
                this.$t("updated_message1") +
                this.$t("label.actors") +
                ` ID: ${this.parent.form.id} ` +
                this.$t("updated_message2"),
              title: {
                update: this.$t("record_updated"),
              },
            };

            this.parent.makeToast(
              "success",
              message.title.update,
              message.update
            );
						this.parent.loadActorInformation(response);
            this.parent.successfullySavedActor();
          }
        })
        .catch((error) => {
          console.log(error);
          let errors = error.response.data.errors;
          this.toastError(errors);
        });
    },

    toastError(error){
      console.log(error);
      let vm = this;
      for (const [key, value] of Object.entries(error)) {
        this.parent.makeToast("danger", vm.$t('errors.error'), value);
      }
    },

		deleteOtherInfoDiv: function(index) {
      let vm = this;

      $.confirm({
        title: this.$t("confirm_action"),
        content: vm.$t("label.delete_other_info_confirmation"),
        type: "red",
        typeAnimated: true,
        buttons: {
          tryAgain: {
            text: this.$t("confirm"),
            btnClass: "btn-red",
            action: function() {
              vm.parent.deleteOtherInfoDiv(index);
            },
          },
          cancel: function() {},
        },
      });
    },
  },
};
</script>
