// Component Display Title
Vue.component('wp-views-display-title', {
  template: `<div class="wp__views_settings_panel wp__views_settings_panel_title">
    <h3 class="wp__views_panel_title">{{ panelTitle }}</h3>
    <div class="wp__views_panel_body clearfix">
      <span class="label">{{ panelTitle }}: </span>
      <a href="#" class="wp__views_ajax_link" v-html="displayTitle"></a>
    </div>
  </div>`,
  props: {
      panelTitle: {
          type: String,
          default: ''
      },
      displayTitle: {
          type: String,
          default: ''
      },
  },
  data: function() {
      return {

      };
  },
  computed: {
      
  },
  methods: {
      getClass: function(i) {
        var classes = ['ae__' + i];
          return classes;
      },
  },
  created: function() {

  },
  filters: {
      
  },
  mounted: function() {
      var vm = this;
  },
});

// Component Display Format
Vue.component('wp-views-display-format', {
  template: `<div class="wp__views_settings_panel wp__views_settings_panel_format">
    <h3 class="wp__views_panel_title">{{ panelTitle }}</h3>
    <div class="wp__views_panel_body clearfix">
      <span class="label">{{ panelTitle }}: </span>
      <a href="#" class="wp__views_ajax_link">{{ displayFormat }}</a><span class="label">&nbsp;|&nbsp;</span><a href="#" class="wp__views_ajax_link">{{ settingText }}</a>
    </div>
  </div>`,
  props: {
      panelTitle: {
          type: String,
          default: ''
      },
      displayFormat: {
          type: String,
          default: ''
      },
      settingText: {
          type: String,
          default: ''
      },
  },
  data: function() {
      return {

      };
  },
  computed: {
      
  },
  methods: {
      getClass: function(i) {
        var classes = ['ae__' + i];
          return classes;
      },
  },
  created: function() {

  },
  filters: {
      
  },
  mounted: function() {
      var vm = this;
  },
});