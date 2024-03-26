// eslint-disable-next-line no-undef
module.exports = {
  env: {
    browser: true,
    es2021: true,
  },
  extends: ["eslint:recommended", "plugin:vue/vue3-recommended", "prettier"],
  parserOptions: {
    ecmaVersion: "latest",
    sourceType: "module"
  },
  plugins: ["vue", "simple-import-sort", "prettier"],
  globals: {
    visit: "readonly",
    route: "readonly",
    filters: "readonly",
    require: true,
    _: true,
    IS_PROD: true,
    APR_FACTOR: true,
    $echo: true,
    axios: true
  },
  rules: {
    "require-jsdoc": "warn",
    "valid-jsdoc": "warn",
    "sort-imports": ["error", {ignoreDeclarationSort: true, ignoreCase: true}],
    "simple-import-sort/imports": "error",
    "vue/no-unused-components": "warn",
    "no-unused-vars": [
      "warn", // or error
      {
        varsIgnorePattern: "^_",
        argsIgnorePattern: "visit",
      },
    ],
    "vue/no-v-html": "off",
    "vue/no-reserved-component-names": "off",
    "vue/name-property-casing": "off",
    "vue/multi-word-component-names": "off",
    "vue/attributes-order": "error",
    "vue/html-quotes": "error",
    "vue/mustache-interpolation-spacing": ["error", "always"],
    "vue/no-side-effects-in-computed-properties": "error",
    "vue/order-in-components": [
      "error",
      {
        order: [
          "el",
          "name",
          "parent",
          "LIFECYCLE_HOOKS",
          "methods",
          ["template", "render"],
          "renderError",
        ],
      },
    ],
    "vue/prop-name-casing": [0, "snake_case"],
    "vue/require-default-prop": "off",
    "vue/require-prop-types": "warn",
    "vue/require-valid-default-prop": "error",
    "vue/return-in-computed-property": "error",
    "prettier/prettier": ["error", {bracketSameLine: false}]
  },
};
