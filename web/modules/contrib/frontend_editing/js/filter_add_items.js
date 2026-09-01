(function (Drupal) {
  /**
   * Filter paragraphs.
   *
   * @type {{attach: Drupal.behaviors.frontendEditingFilterParagraphs.attach}}
   */
  Drupal.behaviors.frontendEditingFilterParagraphs = {
    attach(context, settings) {
      const [input] = once(
        'search-filter-text',
        'input[name="search"]',
        context,
      );
      if (!input) {
        return;
      }

      input.focus();

      const $list = document.querySelector('.paragraphs-add-dialog-list');
      const $rows = document.querySelectorAll('.paragraphs-add-dialog-list li');

      function filterList(e) {
        const query = e.target.value;
        if (query.length === 0) {
          // Reset table when the textbox is cleared.
          $rows.forEach((row, index) => {
            row.style.display = 'list-item';
          });
        }
        // Case insensitive expression to find query at the beginning of a word.
        const re = new RegExp(`${query}`, 'i');
        function showPermissionRow(index, row) {
          const sources = row.querySelectorAll('.paragraphs-add-dialog-row a');
          if (sources.length > 0) {
            const textMatch = sources[0].textContent.search(re) !== -1;
            if (textMatch) {
              row.style.display = 'list-item';
            } else {
              row.style.display = 'none';
            }
          }
        }
        // Search over all rows.
        $rows.forEach((row, index) => {
          row.style.display = 'list-item';
        });

        // Filter if the length of the query is at least 2 characters.
        if (query.length >= 2) {
          $rows.forEach((row, index) => {
            showPermissionRow(index, row);
          });
        }
      }

      function preventEnterKey(event) {
        if (event.which === 13) {
          event.preventDefault();
          event.stopPropagation();
        }
      }

      if ($list) {
        $list.addEventListener('keyup', filterList, false);
        $list.addEventListener('debounce', filterList, false);
        $list.addEventListener('keydown', preventEnterKey, false);
      }
    },
  };
})(Drupal);
