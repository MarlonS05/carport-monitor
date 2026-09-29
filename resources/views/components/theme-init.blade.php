<script>
(function () {
    var STORAGE_KEY = 'carport-monitor-theme';
    var DEFAULT_THEME = 'legacy';
    var VALID_THEMES = {
        legacy: true,
        oceanDepth: true,
        swampFog: true,
        subZero: true,
        mountainSunrise: true,
    };
    var MIGRATION = {
        darkColorful: 'legacy',
        darkLightBlue: 'oceanDepth',
        darkPlain: 'mountainSunrise',
        lightPlain: 'subZero',
        lightColorful: 'mountainSunrise',
    };

    function normalizeTheme(theme) {
        if (MIGRATION[theme]) {
            return MIGRATION[theme];
        }

        return VALID_THEMES[theme] ? theme : DEFAULT_THEME;
    }

    function updatePickerUi(activeTheme) {
        var picker = document.querySelector('[data-theme-picker]');

        if (picker) {
            picker.value = activeTheme;
        }
    }

    function applyTheme(theme) {
        var normalized = normalizeTheme(theme);

        document.documentElement.setAttribute('data-theme', normalized);
        localStorage.setItem(STORAGE_KEY, normalized);
        updatePickerUi(normalized);
    }

    function initThemePicker() {
        var stored = localStorage.getItem(STORAGE_KEY);

        applyTheme(stored || DEFAULT_THEME);
    }

    window.CarportTheme = { applyTheme: applyTheme };

    document.addEventListener('change', function (event) {
        var picker = event.target.closest('[data-theme-picker]');

        if (picker) {
            applyTheme(picker.value);
        }
    });

    var storedTheme = localStorage.getItem(STORAGE_KEY);
    var activeTheme = normalizeTheme(storedTheme || DEFAULT_THEME);

    if (storedTheme !== activeTheme) {
        localStorage.setItem(STORAGE_KEY, activeTheme);
    }

    document.documentElement.setAttribute('data-theme', activeTheme);

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initThemePicker);
    } else {
        initThemePicker();
    }
})();
</script>
