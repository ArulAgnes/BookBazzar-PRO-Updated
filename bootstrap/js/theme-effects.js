/* BookBazzar theme effects: wraps every book cover image in a 3D tilt
   card, tracking the mouse so covers gently rotate towards the cursor.
   Loaded once from template/footer.php - applies to every page. */
(function () {
  function init() {
    var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var images = document.querySelectorAll('img.img-thumbnail, img.admin-thumb');

    images.forEach(function (img) {
      if (img.parentElement && img.parentElement.classList.contains('tilt-card')) {
        return; // already wrapped
      }
      var wrapper = document.createElement('span');
      wrapper.className = 'tilt-card';
      wrapper.style.display = 'inline-block';
      img.parentNode.insertBefore(wrapper, img);
      wrapper.appendChild(img);

      if (reduce) { return; }

      wrapper.addEventListener('mousemove', function (e) {
        var rect = wrapper.getBoundingClientRect();
        var x = (e.clientX - rect.left) / rect.width - 0.5;
        var y = (e.clientY - rect.top) / rect.height - 0.5;
        img.style.transform = 'rotateY(' + (x * 18) + 'deg) rotateX(' + (y * -18) + 'deg) scale(1.04)';
      });
      wrapper.addEventListener('mouseleave', function () {
        img.style.transform = 'rotateY(0deg) rotateX(0deg) scale(1)';
      });
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
