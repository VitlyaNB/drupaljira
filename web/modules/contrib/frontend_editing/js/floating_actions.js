/**
 * Keeps editing actions in viewport.
 */

(function (Drupal) {
  // Prevents the need to query the DOM multiple times
  const actionsContainerMap = new Map();

  const getActionsContainer = (element) => {
    if (!actionsContainerMap.has(element)) {
      // Do not select actions inside child frontend-editing elements
      const actions = element.querySelector(
        '.common-actions-container:not(:scope .frontend-editing .common-actions-container)',
      );
      actionsContainerMap.set(element, actions);
    }
    return actionsContainerMap.get(element);
  };

  const updatePositionReset = (element) => {
    const actions = getActionsContainer(element);

    actions.removeAttribute('style');
    actions.classList.remove('place-fixed');
  };

  const updatePositionFixed = (element, rect) => {
    const { left, top } = rect;

    // If the element top is above the viewport
    if (top <= 0) {
      const actions = getActionsContainer(element);

      updatePositionReset(element);
      actions.classList.add('place-fixed');
      actions.style.left = `${left}px`;
    }
  };

  // Observer for the top of the element
  const topObserver = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        const element = entry.target;
        const rect = element.getBoundingClientRect();

        if (entry.isIntersecting) {
          // Top left viewport
          updatePositionFixed(element, rect);
        } else if (rect.bottom > 0) {
          // Top entered viewport
          updatePositionReset(element);
        }
      });
    },
    {
      root: null,
      threshold: 0,
      rootMargin: '0px 0px -100% 0px',
    },
  );

  Drupal.behaviors.editingActionsPosition = {
    attach(context) {
      context.querySelectorAll('.frontend-editing').forEach((element) => {
        topObserver.observe(element);
      });
    },
  };
})(Drupal);
