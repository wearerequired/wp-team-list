module.exports = {
  dist: {
    options: {
      textdomain   : 'rplus-wp-team-list',
      updateDomains: []
    },
    target : {
      files: {
        src: ['*.php', '**/*.php', '!node_modules/**', '!tests/**']
      }
    }
  }
};