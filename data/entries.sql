    INSERT INTO articles
    (title, description, content, content_raw)
    VALUES (
        'Object Oriented OpenGL: Thing',
        'Using OpenGL with an object oriented approach',
        '<p>Object oriented programming is one of the greatest gifts god gave to us humans. Using OpenGL, the big state machine, it is inevitable to miss it. So, in this article I am going to explain how I wrapped essential OpenGL \&quot;objects\&quot; in actual C++ classes, at times grouping some together. Let\'s go over the process of rendering something and pack them into classes dfson our way.</p>
<p>We first create and bind a Vertex Array Object to bind the OpenGL \&quot;objects\&quot; to come (vbo and ebo). Its purpose is to keep track of the properties of a specific thing to be drawn and bind them when bound itself.</p>
<pre><code>unsigned int vao;
glGenVertexArrays(1, &amp;vao);
glBindVertexArray(vao);</code></pre>
<p>Then, we create and fill a Vertex Buffer Object and an Element Buffer Object. First of which will contain vertex coordinates along with texture image coordinates corresponding to each vertex if we need them (each piece of information called attributes) and the second will contain in which order those vertices are to be drawn, generally called indices (we need this because it is often needed for a vertex to be drawn multiple times as shapes consist of primitives, generally triangles, that often share vertices). These  information will be in arrays and will look like these for a square (consisting of two triangles):</p>
<pre><code>float vertices[] = {
  //x,   y, z, s, t
  -50, -50, 0, 0, 1,
  -50,  50, 0, 0, 0, 
   50,  50, 0, 1, 0,
   50, -50, 0, 1, 1
};
unsigned int indices[] = {

  0, 1, 3,
  3, 1, 2
};</code></pre>
<p>Each vertex here has two attributes: position (x, y, z) and texture coordinates (s, t). Now, on with passing the information and telling how to read it to OpenGL.</p></p>
<pre><code>unsigned int vbo, ebo; //generating our buffers
glGenBuffers(1, &amp;vbo);
glGenBuffers(1, &amp;ebo);

glBindBuffer(GL_ARRAY_BUFFER, vbo); //binding and filling them
glBufferData(GL_ARRAY_BUFFER, sizeof(vertices), vertices, GL_STATIC_DRAW);

glBindBuffer(GL_ELEMENT_ARRAY_BUFFER, ebo);
glBufferData(GL_ELEMENT_ARRAY_BUFFER, sizeof(indices), indices, GL_STATIC_DRAW);

//\"explaining\" what attributes we pass:
glVertexAttribPointer(0, 3, GL_FLOAT, GL_FALSE, 5 * sizeof(float), (void*)0); //we have position coordinates (3 floats)
glEnableVertexAttribArray(0);

glVertexAttribPointer(1, 2, GL_FLOAT, GL_FALSE, 5 * sizeof(float), (void*)(3 * sizeof(float))); //and texture coordinates (2 floats)
glEnableVertexAttribArray(1);</code></pre>
<p>The function glVertexAttribPointer may look complicated but all it does is ask for instructions how to read each vertex data we just passed to OpenGL and  all it takes is nothing more than information we already know:</p>
<pre><code>glVertexAttribPointer(
    which attribute we are specifying (location), 
    how many elements it has,
    what type those elements are
    whether our data should be normalized to unit range,
    size of all attributes combined for a single vertex,
    position offset for the current attribute
);</code></pre>
<p>All of the prior information we set are now linked to the vao we created and all that is needed to be done to bind them is binding the vao. It only makes sense to create a class for vao. Let\'s call it Thing.</p>
<pre><code>class Thing{

  unsigned int vao, elementCount;

  public:

    Thing(float vertices[], unsigned int vertexCount; unsigned int indices[], unsigned int indexCount);
    void display();
};</code></pre>
<pre><code>Thing::Thing(float vertices[], unsigned int vertexCount; unsigned int indices[], unsigned int indexCount){

  elementCount = indexCount;
  //...
  glBufferData(GL_ARRAY_BUFFER, vertexCount * sizeof(float), &amp;vertices[0], GL_STATIC_DRAW);
  //...
  glBufferData(GL_ELEMENT_ARRAY_BUFFER, indexCount * sizeof(unsigned int), &amp;indices[0], GL_STATIC_DRAW);
}

void Thing::display(){

  glBindVertexArray(vao);
  glDrawElements(GL_TRIANGLES, elementCount, GL_UNSIGNED_INT, 0);
}</code></pre>
<h2>Shaders</h2>
<p>For this -or anything in modern OpenGL- to work we need to write at least a fragment and a vertex shader. Let\'s write simple shaders suitable for the vao class we just created.</p></p>
<pre><code>#version 330 core
//the attributes we defined:
layout (location = 0) in vec3 position;
layout (location = 1) in vec2 textureCoordinates;
out vec2 textureC;
uniform mat4 anyMatrixWeMayHave; //we will discuss this in a later article

void main(){

  gl_Position = anyMatrixWeMayHave * vec4(position, 1.0);
  textureC = textureCoordinates;
}</code></pre>
<pre><code>#version 330 core
out vec4 FragColor;
in vec2 textureC;
uniform sampler2D textureSampler;

void main(){

  FragColor = texture(textureSampler, textureC);
}</code></pre>
<h2>Instancing</h2>
<p>Instancing is an essential technique in graphics programming. It allows rendering multiples of an object with a single draw call, reducing the cost of rendering process drastically at times. Let\'s implement this in our class.</p>
<p>We will need a member variable to keep track of the instances to be rendered, and a function to pass the positions with that adds a third attribute.</p>
<pre><code>void Thing::instance(glm::vec3 positions[], unsigned int count){

  count = count_; 
  if(count != 0){

    unsigned int vbo2;
    glGenBuffers(1, &amp;vbo2);

    glBindVertexArray(vao);
    glBindBuffer(GL_ARRAY_BUFFER, vbo2);
    glBufferData(GL_ARRAY_BUFFER, count * sizeof(glm::vec3), &amp;positions[0], GL_STATIC_DRAW);

    glVertexAttribPointer(2, 3, GL_FLOAT, GL_FALSE, 3 * sizeof(float), (void*)0);
    glEnableVertexAttribArray(2);

    glVertexAttribDivisor(2, 1);
  }
}</code></pre>
<p>We will also need to update the display function, making it use the appropriate function depending on the count.</p>
<pre><code>void Thing::display(){

  glBindVertexArray(vao);
  if(count == 1) glDrawElements(GL_TRIANGLES, elementCount, GL_UNSIGNED_INT, 0);
  else if(count &gt; 1) glDrawElementsInstanced(GL_TRIANGLES, elementCount, GL_UNSIGNED_INT, 0, count);
}</code></pre>
<p>Lastly, we need to update our vertex shader to receive the third attribute we just defined.</p>
<pre><code>//...
layout (location = 2) in vec3 instancePosition;

void main(){

  //...
  gl_Position =  anyMatrixWeMayHave * vec4(position + instancePosition, 1.0);
}</code></pre>
<h2>Example Usage</h2>
<pre><code>float vertices[] = {
  //x,    y, z, s, t
  -100, -50, 0, 0, 1,
     0,  50, 0, 0, 0, 
   100,  50, 0, 1, 0,
     0, -50, 0, 1, 1
};

unsigned int indices[] = {

  0, 1, 3,
  3, 1, 2
};

glm::vec3 positions = {

  glm::vec3(400, 300, 0),
  glm::vec3(500, 400, 0)
};

Thing parallelogram(vertices, sizeof(vertices), indices, sizeof(indices));
parallelogram.instance(positions, sizeof(positions));
parallelogram.display();</code></pre>
<p><img src="static/images/thing.png" alt="Two white parallelograms" /></p>
<p>I hope this was helpful. Feel free to ask or point out anything commenting below. If you\'d like to dive a little deeper into these subjects, I definitely recommend checking <a href="https://learnopengl.com">here</a>. In the next articles on object oriented OpenGL, we will wrap texture and shader concepts in classes.</p>',
        'Object oriented programming is one of the greatest gifts god gave to us humans. Using OpenGL, the big state machine, it is inevitable to miss it. So, in this article I am going to explain how I wrapped essential OpenGL \"objects\" in actual C++ classes, at times grouping some together. Let\'s go over the process of rendering something and pack them into classes dfson our way.

We first create and bind a Vertex Array Object to bind the OpenGL \"objects\" to come (vbo and ebo). Its purpose is to keep track of the properties of a specific thing to be drawn and bind them when bound itself.

```
unsigned int vao;
glGenVertexArrays(1, &vao);
glBindVertexArray(vao);
```

Then, we create and fill a Vertex Buffer Object and an Element Buffer Object. First of which will contain vertex coordinates along with texture image coordinates corresponding to each vertex if we need them (each piece of information called attributes) and the second will contain in which order those vertices are to be drawn, generally called indices (we need this because it is often needed for a vertex to be drawn multiple times as shapes consist of primitives, generally triangles, that often share vertices). These  information will be in arrays and will look like these for a square (consisting of two triangles):

```
float vertices[] = {
  //x,   y, z, s, t
  -50, -50, 0, 0, 1,
  -50,  50, 0, 0, 0, 
   50,  50, 0, 1, 0,
   50, -50, 0, 1, 1
};
unsigned int indices[] = {

  0, 1, 3,
  3, 1, 2
};
```

Each vertex here has two attributes: position (x, y, z) and texture coordinates (s, t). Now, on with passing the information and telling how to read it to OpenGL.</p>

```
unsigned int vbo, ebo; //generating our buffers
glGenBuffers(1, &vbo);
glGenBuffers(1, &ebo);

glBindBuffer(GL_ARRAY_BUFFER, vbo); //binding and filling them
glBufferData(GL_ARRAY_BUFFER, sizeof(vertices), vertices, GL_STATIC_DRAW);

glBindBuffer(GL_ELEMENT_ARRAY_BUFFER, ebo);
glBufferData(GL_ELEMENT_ARRAY_BUFFER, sizeof(indices), indices, GL_STATIC_DRAW);

//\"explaining\" what attributes we pass:
glVertexAttribPointer(0, 3, GL_FLOAT, GL_FALSE, 5 * sizeof(float), (void*)0); //we have position coordinates (3 floats)
glEnableVertexAttribArray(0);

glVertexAttribPointer(1, 2, GL_FLOAT, GL_FALSE, 5 * sizeof(float), (void*)(3 * sizeof(float))); //and texture coordinates (2 floats)
glEnableVertexAttribArray(1);
```

The function glVertexAttribPointer may look complicated but all it does is ask for instructions how to read each vertex data we just passed to OpenGL and  all it takes is nothing more than information we already know:

    glVertexAttribPointer(
        which attribute we are specifying (location), 
        how many elements it has,
        what type those elements are
        whether our data should be normalized to unit range,
        size of all attributes combined for a single vertex,
        position offset for the current attribute
    );

All of the prior information we set are now linked to the vao we created and all that is needed to be done to bind them is binding the vao. It only makes sense to create a class for vao. Let\'s call it Thing.

```
class Thing{

  unsigned int vao, elementCount;
  
  public:
    
    Thing(float vertices[], unsigned int vertexCount; unsigned int indices[], unsigned int indexCount);
    void display();
};
```

```
Thing::Thing(float vertices[], unsigned int vertexCount; unsigned int indices[], unsigned int indexCount){

  elementCount = indexCount;
  //...
  glBufferData(GL_ARRAY_BUFFER, vertexCount * sizeof(float), &vertices[0], GL_STATIC_DRAW);
  //...
  glBufferData(GL_ELEMENT_ARRAY_BUFFER, indexCount * sizeof(unsigned int), &indices[0], GL_STATIC_DRAW);
}

void Thing::display(){

  glBindVertexArray(vao);
  glDrawElements(GL_TRIANGLES, elementCount, GL_UNSIGNED_INT, 0);
}
```

## Shaders

For this -or anything in modern OpenGL- to work we need to write at least a fragment and a vertex shader. Let\'s write simple shaders suitable for the vao class we just created.</p>

```
#version 330 core
//the attributes we defined:
layout (location = 0) in vec3 position;
layout (location = 1) in vec2 textureCoordinates;
out vec2 textureC;
uniform mat4 anyMatrixWeMayHave; //we will discuss this in a later article

void main(){

  gl_Position = anyMatrixWeMayHave * vec4(position, 1.0);
  textureC = textureCoordinates;
}
```

```
#version 330 core
out vec4 FragColor;
in vec2 textureC;
uniform sampler2D textureSampler;

void main(){

  FragColor = texture(textureSampler, textureC);
}
```

## Instancing

Instancing is an essential technique in graphics programming. It allows rendering multiples of an object with a single draw call, reducing the cost of rendering process drastically at times. Let\'s implement this in our class.

We will need a member variable to keep track of the instances to be rendered, and a function to pass the positions with that adds a third attribute.

```
void Thing::instance(glm::vec3 positions[], unsigned int count){
  
  count = count_; 
  if(count != 0){

    unsigned int vbo2;
    glGenBuffers(1, &vbo2);

    glBindVertexArray(vao);
    glBindBuffer(GL_ARRAY_BUFFER, vbo2);
    glBufferData(GL_ARRAY_BUFFER, count * sizeof(glm::vec3), &positions[0], GL_STATIC_DRAW);

    glVertexAttribPointer(2, 3, GL_FLOAT, GL_FALSE, 3 * sizeof(float), (void*)0);
    glEnableVertexAttribArray(2);

    glVertexAttribDivisor(2, 1);
  }
}
```

We will also need to update the display function, making it use the appropriate function depending on the count.

```
void Thing::display(){

  glBindVertexArray(vao);
  if(count == 1) glDrawElements(GL_TRIANGLES, elementCount, GL_UNSIGNED_INT, 0);
  else if(count > 1) glDrawElementsInstanced(GL_TRIANGLES, elementCount, GL_UNSIGNED_INT, 0, count);
}
```

Lastly, we need to update our vertex shader to receive the third attribute we just defined.

```
//...
layout (location = 2) in vec3 instancePosition;

void main(){

  //...
  gl_Position =  anyMatrixWeMayHave * vec4(position + instancePosition, 1.0);
}
```

## Example Usage

```
float vertices[] = {
  //x,    y, z, s, t
  -100, -50, 0, 0, 1,
     0,  50, 0, 0, 0, 
   100,  50, 0, 1, 0,
     0, -50, 0, 1, 1
};

unsigned int indices[] = {

  0, 1, 3,
  3, 1, 2
};

glm::vec3 positions = {

  glm::vec3(400, 300, 0),
  glm::vec3(500, 400, 0)
};

Thing parallelogram(vertices, sizeof(vertices), indices, sizeof(indices));
parallelogram.instance(positions, sizeof(positions));
parallelogram.display();
```

![Two white parallelograms](static/images/thing.png)

I hope this was helpful. Feel free to ask or point out anything commenting below. If you\'d like to dive a little deeper into these subjects, I definitely recommend checking [here](https://learnopengl.com). In the next articles on object oriented OpenGL, we will wrap texture and shader concepts in classes.'
    );

    INSERT INTO articles
    (title, description, content, content_raw)
    VALUES (
        'Object Oriented OpenGL: Texture',
        'Using 2D textures and creating a Texture class',
        '<p>This tutorial will explain how to use 2D textures taking from where we left in the <a href="http://localhost/article?id=56">previous tutorial</a>. Our goal will be creating a texture class that is also implemented in the Thing class we previously created.</p>
<p>Like every other OpenGL object,  a texture is represented by an unsigned int ID. It is then bound using that. Every setting and render operation concerning the type of our texture (for the scope of this tutorial GL_TEXTURE_2D) affects or uses our texture.</p>
<p>Our class will be a small one that looks like this:</p>
<pre><code>class Texture{

  unsigned int ID;

public:

  Texture(const char* path);
  void bind();
  ~Texture();
};</code></pre>
<p>Let\'s start filling it starting with creating an OpenGL 2D texture object and loading an image. To load image files, we will use the public domain single header library stb_image. It can be downloaded <a href="https://github.com/nothings/stb/blob/master/stb_image.h">here</a>.</p>
<p>After creating and binding our texture we set wrapping and filtering methods. All of these operations, including image setting, will only affect the texture currently bound.</p>
<pre><code>void Texture::Texture(const char* path){

  glGenTextures(1, &amp;ID);
  glBindTexture(GL_TEXTURE_2D, ID);
  glTexParameteri(GL_TEXTURE_2D, GL_TEXTURE_WRAP_S, GL_REPEAT);
  glTexParameteri(GL_TEXTURE_2D, GL_TEXTURE_WRAP_T, GL_REPEAT);
  glTexParameteri(GL_TEXTURE_2D, GL_TEXTURE_MIN_FILTER, GL_LINEAR);
  glTexParameteri(GL_TEXTURE_2D, GL_TEXTURE_MAG_FILTER, GL_LINEAR);
}</code></pre>
<p>Then we load the file and pass it to OpenGL specifying whether it has an alpha channel depending on the channel count stb_image gives us.</p>
<pre><code>void Texture::Texture(const char* path){
  //...
  int width, height, channelCount;
  stbi_set_flip_vertically_on_load(true); //we need this because the positive y direction of most image formats are the opposite of of OpenGL 
  unsigned char* data = stbi_load(path, &amp;width, &amp;height, &amp;channelCount, 0);
  if(data){

    if(channelCount == 3) glTexImage2D(GL_TEXTURE_2D, 0, GL_RGB, width, height, 0, GL_RGB, GL_UNSIGNED_BYTE, data);
    else if(channelCount == 4) glTexImage2D(GL_TEXTURE_2D, 0, GL_RGBA, width, height, 0, GL_RGBA, GL_UNSIGNED_BYTE, data);
    glGenerateMipmap(GL_TEXTURE_2D);
  }
  else std::cout &lt;&lt; \"Failed to load texture.\" &lt;&lt; std::endl;
}</code></pre>
<p>We can unbind the texture and free the image data now that it has been passed to OpenGL.</p>
<pre><code>void Texture::Texture(const char* path){
  //...
  glBindTexture(GL_TEXTURE_2D, 0);
  stbi_image_free(data);
}</code></pre>
<p>The bind method will only call the OpenGL bind function.</p>
<pre><code>void Texture::bind(){

  glBindTexture(GL_TEXTURE_2D, ID);
}

Texture::~Texture(){

  glDeleteTextures(1, &amp;ID);
}</code></pre>
<h2>Implementing to Thing class</h2>
<p>It would be nice to have the thing objects bind their textures before they are displayed, for a lot of times a Thing will always be drawn with a specific texture bound.</p>
<p>Let\'s introduce our class two new members: a Texture pointer to fill when there is a texture attached, and a boolean to keep track of this.</p>
<pre><code>class Thing{
  //...
  bool textureSet = false;
  Texture* texture;
};</code></pre>
<p>We will also create a function that takes an image file path as a parameter, creates a Texture object and updates the boolean.</p>
<pre><code>void Thing::setTexture(const char* path){

  texture = new Texture;
  texture-&gt;load(path);
  textureSet = true;
}</code></pre>
<p>Lastly, updating the display function to bind the texture if there is one set.</p>
<pre><code>void Thing::display(){

  if(textureSet) texture-&gt;bind();
  //...
}</code></pre>
<p>Let\'s use what we\'ve created here and give our parallelograms [a texture]().</p>
<pre><code>Thing parallelogram(vertices, sizeof(vertices), indices, sizeof(indices));
parallelogram.setTexture(\"grass.png\");
parallelogram.instance(positions, sizeof(positions));
parallelogram.display();</code></pre>',
        'This tutorial will explain how to use 2D textures taking from where we left in the [previous tutorial](http://localhost/article?id=56). Our goal will be creating a texture class that is also implemented in the Thing class we previously created.

Like every other OpenGL object,  a texture is represented by an unsigned int ID. It is then bound using that. Every setting and render operation concerning the type of our texture (for the scope of this tutorial GL_TEXTURE_2D) affects or uses our texture.

Our class will be a small one that looks like this:

```
class Texture{

  unsigned int ID;

public:

  Texture(const char* path);
  void bind();
  ~Texture();
};
```

Let\'s start filling it starting with creating an OpenGL 2D texture object and loading an image. To load image files, we will use the public domain single header library stb_image. It can be downloaded [here](https://github.com/nothings/stb/blob/master/stb_image.h).

After creating and binding our texture we set wrapping and filtering methods. All of these operations, including image setting, will only affect the texture currently bound.

```
void Texture::Texture(const char* path){

  glGenTextures(1, &ID);
  glBindTexture(GL_TEXTURE_2D, ID);
  glTexParameteri(GL_TEXTURE_2D, GL_TEXTURE_WRAP_S, GL_REPEAT);
  glTexParameteri(GL_TEXTURE_2D, GL_TEXTURE_WRAP_T, GL_REPEAT);
  glTexParameteri(GL_TEXTURE_2D, GL_TEXTURE_MIN_FILTER, GL_LINEAR);
  glTexParameteri(GL_TEXTURE_2D, GL_TEXTURE_MAG_FILTER, GL_LINEAR);
}
```

Then we load the file and pass it to OpenGL specifying whether it has an alpha channel depending on the channel count stb_image gives us.

```
void Texture::Texture(const char* path){
  //...
  int width, height, channelCount;
  stbi_set_flip_vertically_on_load(true); //we need this because the positive y direction of most image formats are the opposite of of OpenGL 
  unsigned char* data = stbi_load(path, &width, &height, &channelCount, 0);
  if(data){

    if(channelCount == 3) glTexImage2D(GL_TEXTURE_2D, 0, GL_RGB, width, height, 0, GL_RGB, GL_UNSIGNED_BYTE, data);
    else if(channelCount == 4) glTexImage2D(GL_TEXTURE_2D, 0, GL_RGBA, width, height, 0, GL_RGBA, GL_UNSIGNED_BYTE, data);
    glGenerateMipmap(GL_TEXTURE_2D);
  }
  else std::cout << \"Failed to load texture.\" << std::endl;
}
```

We can unbind the texture and free the image data now that it has been passed to OpenGL.

```
void Texture::Texture(const char* path){
  //...
  glBindTexture(GL_TEXTURE_2D, 0);
  stbi_image_free(data);
}
```

The bind method will only call the OpenGL bind function.

```
void Texture::bind(){

  glBindTexture(GL_TEXTURE_2D, ID);
}

Texture::~Texture(){

  glDeleteTextures(1, &ID);
}
```

## Implementing to Thing class


It would be nice to have the thing objects bind their textures before they are displayed, for a lot of times a Thing will always be drawn with a specific texture bound.

Let\'s introduce our class two new members: a Texture pointer to fill when there is a texture attached, and a boolean to keep track of this.


```
class Thing{
  //...
  bool textureSet = false;
  Texture* texture;
};
```

We will also create a function that takes an image file path as a parameter, creates a Texture object and updates the boolean.

```
void Thing::setTexture(const char* path){

  texture = new Texture;
  texture->load(path);
  textureSet = true;
}
```

Lastly, updating the display function to bind the texture if there is one set.

```
void Thing::display(){

  if(textureSet) texture->bind();
  //...
}
```

Let\'s use what we\'ve created here and give our parallelograms [a texture]().

```
Thing parallelogram(vertices, sizeof(vertices), indices, sizeof(indices));
parallelogram.setTexture(\"grass.png\");
parallelogram.instance(positions, sizeof(positions));
parallelogram.display();
```'
    );

    INSERT INTO articles
    (title, description, content, content_raw)
    VALUES (
        'Improving Twenty Twenty-One WordPress Theme',
        'Twenty twenty-one is an amazing theme, but can be just a bit better.',
        '<p>Twenty twenty-one is an amazing theme, but can be just a bit better. For example, it can feel cramped at times with its narrow content area. If you think similarly, this simple guide can help. Alterations described can be simply applied on theme editor and customization menus.</p>
<h2>Reducing -subjectively excessive- default bottom footer margin</h2>
<pre><code>.site-footer{

  margin-top: 0px !important;
  padding-bottom: 0px !important;
}</code></pre>
<h2>Reducing header top padding</h2>
<pre><code>.site-header{

  padding-top: var(--global--spacing-vertical);
}</code></pre>
<h2>Giving header-width to page and post content</h2>
<p>If you are like me and think the default content width is <em>a bit</em> too narrow, you can give the same width as the header by replacing max-width property as done here.</p>
<pre><code class="language-.">post-thumbnail,
.entry-content .wp-audio-shortcode,
.entry-content &gt; *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce),
*[class*=inner-container] &gt; *:not(.entry-content):not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce),
.default-max-width{

  /* max-width: var(--responsive--aligndefault-width); */
  max-width: var(--responsive--alignwide-width);
  /* … */
}</code></pre>
<h2>Removing post meta on post pages</h2>
<p>We are only commenting out inside the footer block to keep the horizontal divider. Whole block can be deleted or commented out to get rid of the divider as well.</p>
<pre><code>&lt;footer class=\"entry-footer default-max-width\"&gt;
  &lt;?php //twenty_twenty_one_entry_meta_footer(); ?&gt;
&lt;/footer&gt;&lt;!-- .entry-footer --&gt;</code></pre>',
        'Twenty twenty-one is an amazing theme, but can be just a bit better. For example, it can feel cramped at times with its narrow content area. If you think similarly, this simple guide can help. Alterations described can be simply applied on theme editor and customization menus.

## Reducing -subjectively excessive- default bottom footer margin

```
.site-footer{

  margin-top: 0px !important;
  padding-bottom: 0px !important;
}
```

## Reducing header top padding

```
.site-header{

  padding-top: var(--global--spacing-vertical);
}
```

## Giving header-width to page and post content

If you are like me and think the default content width is <em>a bit</em> too narrow, you can give the same width as the header by replacing max-width property as done here.

```.
post-thumbnail,
.entry-content .wp-audio-shortcode,
.entry-content > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce),
*[class*=inner-container] > *:not(.entry-content):not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce),
.default-max-width{

  /* max-width: var(--responsive--aligndefault-width); */
  max-width: var(--responsive--alignwide-width);
  /* … */
}
```

## Removing post meta on post pages

We are only commenting out inside the footer block to keep the horizontal divider. Whole block can be deleted or commented out to get rid of the divider as well.

```
<footer class=\"entry-footer default-max-width\">
  <?php //twenty_twenty_one_entry_meta_footer(); ?>
</footer><!-- .entry-footer -->
```'
    );

    INSERT INTO articles
    (title, description, content, content_raw)
    VALUES (
        'About this website',
        'flighty.xyz is a minimalist, article focused blog developed as a course project. Its design aims for clarity.',
        '<p>flighty.xyz is a minimalist, article focused blog developed as a course project. Its design aims for clarity.</p>
<h2>Features</h2>
<ul>
<li>Fully responsive, mobile friendly, and accesible design
<ul>
<li>Dedicated mobile navigation menu</li>
</ul></li>
<li>Dark theme</li>
<li>Admin panel
<ul>
<li>Focused view</li>
<li>Entry creation - articles and pages</li>
<li>Editable and deletable entries</li>
<li>Markdown text formatting</li>
<li>Customizable menu order of pages</li>
</ul></li>
<li>Command line interface superuser creation</li>
</ul>
<h2>Interactive Superuser Creation</h2>
<pre><code class="language-console">you@here:~$ php manage.php createuser</code></pre>
<p>The script will walk you through the process, prompting for username and password. After user creation, you can log in on <em>/admin</em> or <em>/login</em>.</p>
<h2>Deployment</h2>
<p>Required MySQL code for the database is provided under <em>data/</em>. Database credentials must be put into <em>database.php</em>. The website will be now ready to be served with Apache. The folder <em>public/</em> should be served.</p>
<h2>Dependencies</h2>
<ul>
<li><a href="https://parsedown.org">Parsedown</a></li>
</ul>
<p>flighty.xyz uses icons from <a href="https://css.gg">css.gg</a> and the <a href="https://rsms.me/inter">Inter</a> font.</p>',
        'flighty.xyz is a minimalist, article focused blog developed as a course project. Its design aims for clarity.

## Features

* Fully responsive, mobile friendly, and accesible design
  * Dedicated mobile navigation menu
* Dark theme
* Admin panel
  * Focused view
  * Entry creation - articles and pages
  * Editable and deletable entries
  * Markdown text formatting
  * Customizable menu order of pages
* Command line interface superuser creation

## Interactive Superuser Creation

```console
you@here:~$ php manage.php createuser
```
The script will walk you through the process, prompting for username and password. After user creation, you can log in on */admin* or */login*.

## Deployment

Required MySQL code for the database is provided under *data/*. Database credentials must be put into *database.php*. The website will be now ready to be served with Apache. The folder *public/* should be served.

## Dependencies

- [Parsedown](https://parsedown.org)

flighty.xyz uses icons from [css.gg](https://css.gg) and the [Inter](https://rsms.me/inter) font.'
    );

    INSERT INTO pages
    (title, menu_index, content, content_raw)
    VALUES (
        'About Me',
        1,
        '<p>This is flighty. I\'ve rarely written and even more rarely written well. This is my attempt to change that by sharing what I learn and create. I am enthusiastic about aerospace and software engineering. C++ and PHP have been what I\'ve been most into and I\'m currently in a bit of graphics programming craze. So, these will most likely be subjects written on here. I really hope to beafsed helpful.</p>
<p>You can find or reach me on the following places. Please don\'t hesitate to get in touch.</p>',
        'This is flighty. I\'ve rarely written and even more rarely written well. This is my attempt to change that by sharing what I learn and create. I am enthusiastic about aerospace and software engineering. C++ and PHP have been what I\'ve been most into and I\'m currently in a bit of graphics programming craze. So, these will most likely be subjects written on here. I really hope to beafsed helpful.

You can find or reach me on the following places. Please don\'t hesitate to get in touch.'
    );