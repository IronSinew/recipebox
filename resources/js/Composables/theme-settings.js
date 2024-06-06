import { ref } from "vue";

const isDarkMode = ref(localStorage.getItem("theme") === "dark");
// eslint-disable-next-line require-jsdoc
export function useThemeSettings() {
    const toggleDarkMode = () => (isDarkMode.value = !isDarkMode.value);

    return { isDarkMode, toggleDarkMode };
}
