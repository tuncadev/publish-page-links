jQuery(document).ready(function ($) {
  // Handle click on "Remove Selected Links" button
  $('.remove-selected-links').on('click', function (e) {
      e.preventDefault();
      var postId = $(this).data('post-id');
      var button = $(this);
      var selectedLinks = [];

      // Gather selected links
      $(this).closest('tr').next('.outgoing-links-row').find('input[type="checkbox"]:checked').each(function () {
          selectedLinks.push($(this).val());
      });

      // Confirm the action
      if (selectedLinks.length > 0 && confirm('Are you sure you want to remove the selected links from this post?')) {
          $.ajax({
              url: pplAdmin.ajax_url,
              type: 'POST',
              data: {
                  action: 'ppl_remove_selected_links',
                  post_id: postId,
                  links: selectedLinks,
                  nonce: pplAdmin.nonce
              },
              success: function (response) {
                  if (response.success) {
                      alert('Selected links removed successfully.');
                      // Reload the page to reflect changes
                      location.reload();
                  } else {
                      alert('An error occurred while removing selected links.');
                  }
              },
              error: function () {
                  alert('An error occurred while processing the request.');
              }
          });
      } else {
          alert('No links selected or action canceled.');
      }
  });

  // Handle click on title to show/hide outgoing links
  $('.title-link').on('click', function (event) {
      event.preventDefault();
      var postId = $(this).data('post-id');
      $('#links-row-' + postId).toggle();
  });

  // Add event listener for checkboxes
  $('.outgoing-links-row input[type="checkbox"]').on('change', function () {
      if ($(this).is(':checked')) {
          $(this).closest('li').addClass('checked');
      } else {
          $(this).closest('li').removeClass('checked');
      }
  });

  // Search functionality
  $('#search-input').on('keyup', function () {
      var value = $(this).val().toLowerCase();
      var regex = new RegExp(value, 'i');
      
      $('.post-row').each(function () {
          var title = $(this).find('.title-link').text();
          if (regex.test(title)) {
              $(this).show();
              $(this).next('.outgoing-links-row').show();

              // Highlight matched text
              var highlightedTitle = title.replace(regex, function(match) {
                  return '<span class="highlight">' + match + '</span>';
              });
              $(this).find('.title-link').html(highlightedTitle);
          } else {
              $(this).hide();
              $(this).next('.outgoing-links-row').hide();
              // Remove highlight if hidden
              $(this).find('.title-link').html(title);
          }
      });
  });
   // Sort functionality
   $('th.sortable').on('click', function () {
    var column = $(this).index();
    var table = $(this).parents('table');
    var tbody = table.find('tbody');
    var rows = tbody.find('tr.post-row').get();
    var sortDirection = $(this).data('sort') === 'asc' ? 'desc' : 'asc';
    
    rows.sort(function (a, b) {
        var A = $(a).children('td').eq(column).text();
        var B = $(b).children('td').eq(column).text();

        // Determine if the content is numeric
        var numA = parseFloat(A);
        var numB = parseFloat(B);

        if (!isNaN(numA) && !isNaN(numB)) {
            return sortDirection === 'asc' ? numA - numB : numB - numA;
        } else {
            return sortDirection === 'asc' ? A.localeCompare(B) : B.localeCompare(A);
        }
    });

    $.each(rows, function (index, row) {
        tbody.append(row);
        $(row).next('.outgoing-links-row').detach().insertAfter(row);
    });

    // Update sort direction
    $('th.sortable').data('sort', 'asc'); // Reset all to 'asc'
    $(this).data('sort', sortDirection);  // Set current to toggled direction
});
});
