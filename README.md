#PHP Graph Plotter

This is a php library for generating images of graphs
from strings of math notation.

This is a school project and other, better, alternatives probably exists.

At this point the project is far from completion, but if you call createTree
with a string of math notation, you can call the evalu method on the object
returned, and it will evaluate the expression. You can add an x variable in
the expression, and it will be replaced by the argument of the evalu function.
All this functionality is not really tested, but seems to work.
