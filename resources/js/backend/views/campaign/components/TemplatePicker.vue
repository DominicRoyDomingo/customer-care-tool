<template>
  <v-sheet
    class="mx-auto"
    elevation="8"
  >
    <v-slide-group
        :value="parent.selected_template.id"
        class="pa-4"
        show-arrows
    >
      <v-slide-item
        v-for="template_item in root_parent.email_templates"
        :key="'template_' + template_item.id"
        :value="template_item.id"
        v-slot:default="{ active }"
      >
      
        <v-hover>
            <template v-slot:default="{ hover }">
                <v-card
                :elevation="active ? 24 : 5"
                class="ma-4"
                height="600"
                width="300"
                >
                <v-card-title>
                    {{ template_item.template_name }}
                </v-card-title>
                    <v-img
                        class="fill-height"
                        dark
                        :src="template_item.preview"
                    >
                        <v-row
                            class="fill-height"
                            align="center"
                            justify="center"
                        >
                            
                            <v-scale-transition>
                                <v-overlay
                                    :opacity=".5"
                                    :absolute="true"
                                    v-if="active">
                                    <v-icon
                                        color="white"
                                        size="48"
                                        v-text="'mdi-check-circle-outline'"
                                    ></v-icon>
                                </v-overlay>
                            </v-scale-transition>

                            <v-scale-transition>
                                <v-overlay
                                    :opacity=".5"
                                    :absolute="true"
                                    v-if="hover && !active">
                                    <v-btn
                                        icon
                                        fab
                                        @click="preview(template_item)"
                                    >    
                                        <v-icon
                                            color="white"
                                            size="48"
                                            v-text="'mdi-magnify'"
                                        ></v-icon>
                                    </v-btn>
                                    <v-btn
                                        icon
                                        fab
                                        @click="selectTemplate(template_item)"
                                    >    
                                        <v-icon
                                            color="white"
                                            size="48"
                                            v-text="'mdi-cursor-pointer'"
                                        ></v-icon>
                                    </v-btn>
                                </v-overlay>
                            </v-scale-transition>

                        </v-row>
                </v-img>
                </v-card>
                
            </template>
        </v-hover>
      </v-slide-item>
    </v-slide-group>
  </v-sheet>
</template>


<script>
  export default {
    props: ["root_parent", "parent"],
    data: () => ({
      model: null,
    }),
    computed: {
        isMobile() {
            let output = false;

            switch(this.$vuetify.breakpoint.name){
                case "xs":
                case "sm":
                    output = true;
                break;
            }

            return output;
        }
    },
    methods: {
        preview(template_item) {
            this.$emit("preview-template", template_item);
        },

        selectTemplate(template_item) {
            this.$emit("select-template", template_item);
        },
    },
  }
</script>