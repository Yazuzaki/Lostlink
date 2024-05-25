function changeImage(icon) {
    var userIcon = document.getElementById('user-icon');
    var homeIcon = document.getElementById('home-icon');
    var bellIcon = document.getElementById('bell-icon');

    // Reset all icons to their default
    userIcon.src = 'fi-rr-user.png';
    homeIcon.src = 'fi-rr-home.png';
    bellIcon.src = 'fi-rr-bell.png';

    // Change the clicked icon
    switch (icon) {
      case 'user':
        userIcon.src = 'fi-sr-user.png';
        break;
      case 'home':
        homeIcon.src = 'fi-sr-home.png';
        break;
      case 'bell':
        bellIcon.src = 'fi-sr-bell.png';
        break;
      default:
        break;
    }
  }