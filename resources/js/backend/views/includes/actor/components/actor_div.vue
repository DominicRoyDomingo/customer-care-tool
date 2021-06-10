<template>
  <div>
		<v-row>
			<v-col  cols="12" md="8" xs="12">
				<v-card outlined class="profile__cards" min-height="290">
					<v-row>
						<v-col cols="12" md="4" xs="12">
							<div class="text-center">
								<v-avatar color="#ebf4fe" size="180" style="margin: 5px">
									<span
										class="display-3"
										style="margin-bottom: 0; color: #329ef4;"
									>
										{{
											!!(parent.actorInfo.firstname + " " + parent.actorInfo.surname)
												? (parent.actorInfo.firstname + " " + parent.actorInfo.surname)
														.split(/\s/)
														.reduce(
															(response, word) =>
																(response += word.slice(0, 1)),
															""
														)
												: "AA"
										}}
									</span>
								</v-avatar>
							</div>
						</v-col>
						<v-col cols="12" md="8" xs="12" class="profile__info">
							<div style="margin-bottom: 5px">
								<span class="title font-weight-bold text--primary">
									{{ parent.actorInfo.title != null ? parent.actorInfo.title : ""}} {{ parent.actorInfo.fullname }}
								</span>
							</div>
							<div v-if="parent.actorInfo.actor_field_of_professions_items.length != 0">
								<p class="subtitle font-weight-bold text--secondary mb-0">
									{{ $t("label.field_of_profession") }}:
								</p>
								<b-button
									v-for="profession in parent.actorInfo.actor_field_of_professions_items"
									:key="profession.index"
									pill
									variant="secondary"
									class="text-white text-uppercase mr-2 mb-1 mt-1"
									style="cursor: not-allowed"
									size="sm"
									v-html="profession.base_name"
								/>
							</div>
							<div v-if="parent.actorInfo.physical_code_item != null">
								<p class="subtitle font-weight-bold text--secondary mb-0">
									{{ $t("table.physical_code") }}:
								</p>
								<v-row>
									<v-col 
										md="6"
										v-for="phsyicalCode in parent.actorInfo.physical_code_item"
										:key="phsyicalCode.index"
									>
										
										<span class="text-caption font-weight-light text--secondary">
											{{ phsyicalCode.physical_code_and_description[0] }}
										</span>
									</v-col>
								</v-row>
							</div>
							<!-- <div>
								<span class="subtitle font-weight-bold text--secondary">
									{{ $t("label.location") }}:
								</span>
								<span class="subtitle font-weight-light text--secondary">
									{{ parent.profileViewed.location_display }}
								</span>
							</div>
							<div>
								<span class="subtitle font-weight-bold text--secondary">
									{{ $t("label.jobs") }}:
								</span>
								<span class="subtitle font-weight-light text--secondary">
									<span v-if="parent.profileViewed.job_data.jobs_display.length">
										<ul>
										<li
											v-for="(job_display, index) in parent.profileViewed.job_data.jobs_display"
											:key="'job_display' + index"
											style="margin: 0"
										>
											<u v-html="job_display"></u>
										</li>
									</ul>
									</span>  
										<span v-else>
											{{ $t("label.no_jobs_found") }}                                
									</span>
								</span>                 
							</div> -->
						</v-col>
						
					</v-row>
				</v-card>
			</v-col>
			<v-col cols="12" xs="12" md="4">
				<v-card outlined class="profile__cards" max-height="290" height="290">
					<v-row>
						<v-col cols="10">
							<span class="title font-weight-light text--secondary">
								{{ $t("label.other_info") }}
							</span>
						</v-col>
						<v-col cols="2">
              <div class="text-right">
                <v-tooltip bottom>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn
                      icon
                      color="#1976d2"
                      @click.prevent="openInformationListModal(parent.actorInfo.new_other_info_item)"
                      v-bind="attrs"
                      v-on="on"
                    >
                      <v-icon>mdi-square-edit-outline</v-icon>
                    </v-btn>
                  </template>
                  <span>{{ $t("information_type_edit_list_button") }}</span>
                </v-tooltip>
              </div>
            </v-col>
					</v-row>
					<v-divider></v-divider>
					<v-row>
						<v-col v-if="parent.actorInfo.new_other_info_item != null">
							<v-col md="12" v-for="otherInfo in parent.actorInfo.other_info_item" :key="otherInfo.index">
								<p class="text-caption font-weight-bold mb-0">
								{{ otherInfo.type[0].base_name }}
								</p>
								<b-button
									:key="otherInfo.index"
									pill
									block
									variant="secondary"
									class="text-white text-uppercase mr-2 mb-1 mt-1"
									style="cursor: not-allowed"
									size="sm"
									v-html="otherInfo.value"
								/>
							</v-col>
						</v-col>
						<v-col v-else>
							<v-col md="12">
								{{ $t("label.no_other_information_found") }}
							</v-col>
						</v-col>
					</v-row>
				</v-card>
			</v-col>
		</v-row>
		<v-row>
			<v-col md="12">
				<h4 class="mb-0">
					{{ $t("label.terminilogies") }} Connections
					<a
						href="javascript:;"
						@click="on_linked_terms_id"
						class="font-sm ml-2"
						style="margin-top: -0px; position: absolute"
						v-b-tooltip.hover
						:title="`${$t('label.linking_to')} ${$t(
							'label.terminilogies'
						)}`"
					>
						<i class="fa fa-link" aria-hidden="true"></i>
					</a>
				</h4>
				<b-overlay
					id="overlay-background"
					:variant="'light'"
					opacity="0.85"
					:blur="'1px'"
					rounded="sm"
				>
					<TermConnections
						:parent="this"
						:term="parent.actorInfo.actor_terms"
					/>
				</b-overlay>
			</v-col>
		</v-row>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import axios from "axios";

import TermConnections from "../snippets/term_connections_list";

export default {

  props: ["parent"],

	components: {
    TermConnections,
  },

  data() {
    return {
      loading: true,
      vm: this,
    };
  },
  computed: {
  },
  methods: {
    on_linked_terms_id() {
			this.parent.modalId = 'link-term-modal'
      this.parent.$bvModal.show("link-term-modal");
    },

    openInformationListModal(otherInfos) {
			let otherInfosArr = [];

			let params = {
        api_token: this.$user.api_token,
        lang: this.parent.form.language,
        locale: this.$i18n.locale,
      };

			if (otherInfos != null) {
        otherInfos.forEach(function (data) {
          otherInfosArr.push({
            type: data.type[0],
            value: data.value,
          });
        });
      } else {
        otherInfosArr.push({
          type: "",
          value: "",
        });
      }
			
			this.parent.form.other_infos = otherInfosArr;
			this.parent.filterOtherInfoDiv();
			this.parent.modalId = 'information-list-modal';
			this.parent.get_information_type_all(params).then((_) => {
        this.parent.newInformationTypes = this.parent.information_types;
        this.parent.$bvModal.show("information-list-modal");
      });
    },
  },
};
</script>
