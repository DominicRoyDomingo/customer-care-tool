<template>
  <v-row justify="center">
    <v-dialog v-model="parent.selectLangModal" persistent max-width="600px">
      <v-card>
        <v-card-title>
          <span class="headline">Please select language - Seleziona la tua lingua</span>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-row>
              <v-col cols="12" sm="12">
                <v-select
                  v-model="parent.lang"
                  @change="changeVar"
                  :items="langs"
                  item-text="value"
                  item-value="id"
                ></v-select>
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-row>
</template>

<script>

export default {
    props: ['parent'],
    data: () => ({
        selectedOrganization: '',
        locale: "",

        langs: [
          { id: "en", value: "English" },
          { id: "it", value: "Italian" },
          { id: "de", value: "German" }
        ]
    }),
    methods: {
        changeVar() {
          this.$session.set("local_lang", this.parent.lang);
          window.location.href = "/lang/" + this.parent.lang;
          // this.$router.go(0);
        }
    },
};
</script>