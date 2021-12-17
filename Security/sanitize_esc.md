# Data Sanitization/Escaping
https://developer.wordpress.org/themes/theme-security/data-sanitization-escaping/


**esc_html()** échappe une chaîne afin qu'elle ne soit pas analysée en HTML. Des caractères comme <sont convertis &lt;, par exemple. Cela ressemblera au lecteur, mais cela signifie que si la valeur en cours de sortie est <script>alors elle ne sera pas interprétée par le navigateur comme une balise de script réelle.

Utilisez cette fonction chaque fois que la valeur en sortie ne doit pas contenir de code HTML.

**esc_attr()** échappe une chaîne afin de pouvoir l'utiliser en toute sécurité dans un attribut HTML, comme class=""par exemple. Cela empêche une valeur de sortir de l'attribut HTML. Par exemple, si la valeur est "><script>alert();</script>et que vous avez essayé de la sortir dans un attribut HTML, elle fermerait la balise HTML actuelle et ouvrirait une balise de script. C'est dangereux. En échappant à la valeur, il ne sera pas en mesure de fermer l'attribut et la balise HTML et de sortir du HTML non sécurisé.

Utilisez cette fonction lors de la sortie d'une valeur à l'intérieur d'un attribut HTML.

**esc_url()** échappe une chaîne pour vous assurer qu'il s'agit d'une URL valide.

Utilisez cette fonction lors de la sortie d'une valeur à l'intérieur d'un attribut href=""ou src="".

**esc_textarea()** échappe une valeur pour pouvoir l'utiliser en toute sécurité dans un <textarea>élément. En échappant une valeur avec cette fonction, cela empêche qu'une valeur soit sortie à l'intérieur de a <textarea<de fermer l' <textarea>élément et de sortir son propre HTML.

Utilisez cette fonction lors de la sortie d'une valeur à l'intérieur d'un <textarea>élément.

**esc_html()** et **esc_attr()** ont également des versions se terminant par __(), _e()et _x(). Ce sont pour la sortie de chaînes traduisibles.

WordPress a des fonctions, __(), _e()et _x(), pour produire du texte qui peut être traduit. 
  - **__()** renvoie une chaîne traduisible,  
  - **_e()** fait écho à une chaîne traduisible 
  - **_x()**renvoie une chaîne traduisible avec un contexte donné. Vous les avez probablement déjà vus.

Étant donné que vous ne pouvez pas nécessairement faire confiance à un fichier de traduction pour contenir des valeurs sûres, l'utilisation de ces fonctions lors de la sortie d'une chaîne traduisible garantit que les chaînes en cours de sortie ne peuvent pas provoquer le même problème décrit ci-dessus.

Utilisez ces fonctions lors de la sortie de chaînes traduisibles.
