<template>
  <div class="form-group">
    <template v-if="info_type.type_of_data === 'Long Text'">
      <v-textarea
        :rules="[rules.required]"
        v-model="information"
        :label="$t('long_text')"
      />
    </template>
    <template v-if="info_type.type_of_data === 'Short Text'">
      <v-text-field
        v-model="information"
        :rules="[rules.required]"
        :label="$t('short_text')"
      />
    </template>
    <template v-if="info_type.type_of_data === 'Email Format'">
      <v-text-field
        v-model="information"
        :rules="[rules.required, rules.email]"
        :label="$t('label.email')"
      />
    </template>
    <template v-if="info_type.type_of_data === 'Numeric'">
      <v-text-field
        v-model="information"
        :rules="[rules.required, rules.number]"
        single-line
        type="number"
        :label="$t('numeric')"
      />
    </template>
    <template v-if="info_type.type_of_data === 'Date'">
      <v-menu content-class="elevation-0">
        <template v-slot:activator="{ on }">
          <v-text-field
            v-model="information"
            v-on="on"
            :label="$t('date')"
            :rules="[rules.required]"
          />
        </template>
        <v-date-picker v-model="information" elevation="15" />
      </v-menu>
    </template>
  </div>
</template>

<script>
export default {
  props: {
    info_type: {
      type: Object,
    },
    index: {
      type: Number,
    },
    infoModel: {
      type: String,
    },
  },

  data() {
    return {
      information: this.infoModel ?? "",
      rules: {
        required: (value) => !!value || "Required.",
        counter: (value) => value.length <= 20 || "Max 20 characters",
        email: (value) => {
          const pattern = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
          return pattern.test(value) || "Invalid email.";
        },
        number: (value) =>
          Number.isInteger(Number(value)) || "The value must be numeric",
      },
    };
  },

  watch: {
    information(value) {
      this.$emit("info-value", value, this.index);
    },
  },
};
</script>
