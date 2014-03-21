(function() {
   tinymce.create('tinymce.plugins.buddyforms', {
      init : function(ed, url) {
         ed.addButton('buddyforms', {
            title : 'BuddyForms',
            image : url+'/buddyformsbutton.png',
            onclick : function() {
               var posts = prompt("Number of posts", "1");
               var text = prompt("List Heading", "This is the heading text");

               if (text != null && text != ''){
                  if (posts != null && posts != '')
                     ed.execCommand('mceInsertContent', false, '[recent-posts posts="'+posts+'"]'+text+'[/recent-posts]');
                  else
                     ed.execCommand('mceInsertContent', false, '[recent-posts]'+text+'[/recent-posts]');
               }
               else{
                  if (posts != null && posts != '')
                     ed.execCommand('mceInsertContent', false, '[recent-posts posts="'+posts+'"]');
                  else
                     ed.execCommand('mceInsertContent', false, '[recent-posts]');
               }
            }
         });
      },
      createControl : function(n, cm) {
         return null;
      },
      getInfo : function() {
         return {
            longname : "BuddyForms",
            author : 'Sven Lehnert',
            authorurl : 'http://themekraft.com',
            infourl : 'http://themekraft.com',
            version : "1.0"
         };
      }
   });
   tinymce.PluginManager.add('buddyforms', tinymce.plugins.buddyforms);
})();