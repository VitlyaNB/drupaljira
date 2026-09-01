(function (Drupal) {
  'use strict';

  Drupal.behaviors.kanbanDragDrop = {
    attach: function (context, settings) {
      const cards = context.querySelectorAll('.kanban-card');
      const columns = context.querySelectorAll('.kanban-column');

      cards.forEach((card) => {
        card.setAttribute('draggable', 'true');

        card.addEventListener('dragstart', (e) => {
          e.dataTransfer.setData('text/plain', card.dataset.nid);
          card.classList.add('dragging');
        });

        card.addEventListener('dragend', () => {
          card.classList.remove('dragging');
        });
      });

      columns.forEach((column) => {
        column.addEventListener('dragover', (e) => {
          e.preventDefault();
          column.classList.add('drag-over');
        });

        column.addEventListener('dragleave', () => {
          column.classList.remove('drag-over');
        });

        column.addEventListener('drop', (e) => {
          e.preventDefault();
          column.classList.remove('drag-over');

          const nid = e.dataTransfer.getData('text/plain');
          const newStatus = column.dataset.status;
          const card = document.querySelector(`.kanban-card[data-nid="${nid}"]`);

          if (card && newStatus) {
            column.querySelector('.kanban-cards-wrapper').appendChild(card);

            fetch(`/api/task/${nid}/update-status`, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
              },
              body: JSON.stringify({ status: newStatus }),
            })
              .then((response) => response.json())
              .then((data) => {
                if (!data.success) {
                  console.error('Failed to update status', data);
                }
              })
              .catch((err) => console.error(err));
          }
        });
      });
    }
  };
})(Drupal);
