// Override the default template set
CKEDITOR.addTemplates('default', {
  // The name of sub folder which hold the shortcut preview images of the
  // templates.
  imagesPath: '/admin/osu_rich/images/',

  // The templates definitions.
  templates: [
    {
      title: 'Quote',
      image: 'quote.png',
      description: 'A quotation style with optional attribution.',
      html: '<div class="rich quote"><div class="quote--text">"{{ Quote Here }}"</div><div class="quote--attribution">- {{ Who Said It }} - {{ Title or Context }}</div></div>'
    }
  ]
});
