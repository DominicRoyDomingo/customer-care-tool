import { required, email, min, max } from "vee-validate/dist/rules";
import { extend, setInteractionMode } from "vee-validate";

setInteractionMode("eager");

extend("required", {
  ...required,
  message: "{_field_} is required",
});

extend("min", {
  ...min,
  message: "{_field_} must be at least {length} characters",
});

extend("max", {
  ...max,
  message: "{_field_} should be lesser than {length} characters",
});

extend("email", {
  ...email,
  message: "{_field_} must be valid (i.e. email@example.com)",
});

extend("password", {
  params: ["target"],
  validate(value, { target }) {
    return value === target;
  },
  message: "Passwords do not match",
});
