// ==UserScript==
// @name        Whom to Follow
// @namespace   http://perk.ee/code
// @version     0.1
// @homepage    http://perk.ee/code/#!/whom
// @description Changes the "Who to Follow" link to "Whom to follow"
// @include     http://twitter.com/*
// @include     https://twitter.com/*
  document.getElementsByClassName('wtf-module')[0].childNodes[1].childNodes[1].childNodes[1].innerHTML = 'Whom to Follow';
// ==/UserScript==