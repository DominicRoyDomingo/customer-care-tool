<template>
  <div class="form-inline local-settings" style="color: #73818f">
    <v-menu offset-y nudge-left="5" nudge-bottom="14" v-model="isMenuOpened">
      <template v-slot:activator="{ on, attrs }">
        <v-btn
          icon
          width="auto"
          height="auto"
          class="mr-1"
          @click="onRemovePopover"
          v-bind="attrs"
          v-on="on"
        >
          <v-avatar
            color="#54c0ff"
            width="36"
            height="26"
            tile
            style="border-radius: 5px !important; min-width: 36px"
            v-if="isLangClicked"
          >
            <img :src="selectedLang.img" />
          </v-avatar>
          <div style="width: 36px" v-else>
            <v-icon color="#fff">mdi-translate</v-icon>
          </div>
        </v-btn>
      </template>
      <v-list style="padding: 0" color="#56bfff">
        <v-list-item
          v-for="(item, index) in langs"
          :key="index"
          style="padding: 0px 5px; min-height: 40px"
        >
          <v-btn
            icon
            width="auto"
            height="auto"
            @click="changeLocalLanguage(item.id)"
          >
            <v-avatar
              color="#54c0ff"
              width="36"
              height="26"
              tile
              style="border-radius: 5px !important; min-width: 36px"
            >
              <img :src="item.img" />
            </v-avatar>
          </v-btn>
        </v-list-item>
      </v-list>
    </v-menu>
    <!-- 
    <label>{{$t('select_local_language')}}</label>
    &nbsp;
    <select v-model="$i18n.locale" class="form-control" @change="changeVar">
      <option v-for="(lang, i) in langs" :key="`Lang${i}`" :value="lang.id">{{ lang.id.toUpperCase() }}</option>
    </select> -->
  </div>
</template>

<script>
import medicalMixin from "../medical_stuff/mixins/medicalMixin";
export default {
  mixins: [medicalMixin],
  data: () => {
    return {
      locale: "",

      isMenuOpened: "",

      isLangClicked: true,

      // selectedLang: [],

      langs: [
        {
          id: "en",
          value: "English",
          img:
            "https://cdn.britannica.com/25/4825-004-C11466B0/Flag-United-Kingdom.jpg",
        },
        {
          id: "it",
          value: "Italian",
          img:
            "https://upload.wikimedia.org/wikipedia/en/thumb/0/03/Flag_of_Italy.svg/1200px-Flag_of_Italy.svg.png",
        },
        {
          id: "de",
          value: "German",
          img:
            "https://upload.wikimedia.org/wikipedia/en/thumb/b/ba/Flag_of_Germany.svg/220px-Flag_of_Germany.svg.png",
        },
        {
          id: "ph-fil",
          value: "Tagalog",
          img:
            "https://upload.wikimedia.org/wikipedia/commons/thumb/9/99/Flag_of_the_Philippines.svg/1200px-Flag_of_the_Philippines.svg.png",
        },
        {
          id: "ph-bis",
          value: "Visayan",
          img: "https://i.redd.it/n1rtishmecvz.png",
        },
      ],
    };
  },

  mounted() {
    // this.selectedLang = this.langs.find(x => x.id === this.$i18n.locale);
  },

  watch: {
    isMenuOpened(menu_open) {
      if (menu_open === true) {
        this.isLangClicked = false;
      }

      if (menu_open === false) {
        this.isLangClicked = true;
      }
    },
  },

  computed: {
    selectedLang() {
      return this.langs.find((x) => x.id === this.$i18n.locale);
    },
  },
  methods: {
    changeLocalLanguage(id) {
      this.$i18n.locale = id;
      this.$session.set("local_lang", this.$i18n.locale);
      window.location.href = "/lang/" + this.$i18n.locale;
      // this.$router.go(0);
    },
    onRemovePopover() {},
  },
};
</script>