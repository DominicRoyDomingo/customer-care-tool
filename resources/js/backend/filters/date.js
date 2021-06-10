import Vue from "vue";

Vue.filter("toLocaleString", function(date) {
  if (!date) {
    return "";
  }

  const options = {
    year: "numeric",
    month: "2-digit",
    day: "2-digit",
  };
  const d = new Date(date);

  return d.toLocaleString("en", options);
});

Vue.filter("toLocaleStringDateTime", function(date) {
  if (!date) {
    return "";
  }
  const options = {
    year: "numeric",
    month: "2-digit",
    day: "2-digit",
    hour: "2-digit",
    minute: "2-digit",
  };
  const d = new Date(date);

  return d.toLocaleString("en", options);
});


Vue.filter("toLocaleTime", function(date) {
  if (!date) {
    return "";
  }
  const options = {
    hour: "2-digit",
    minute: "2-digit",
  };
  
  const d = new Date(date);

  return d.toLocaleString("en", options);
});
