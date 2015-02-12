module.exports = {
  dist: {
    options: {
      textdomain   : 'wp-team-list',
      updateDomains: []
    },
    target : {
      files: {
        src: ['*.php', '**/*.php', '!node_modules/**', '!tests/**']
      }
    }
  }
};