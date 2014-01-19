function Shape(canvas, x1, y1, outline_width, outline_color, fill_color) {
	if (canvas)
		this.context = canvas.getContext("2d");
	this.x1 = x1;
	this.y1 = y1;
	this.x2 = x1;
	this.y2 = y1;
	this.outline_width = outline_width;
	this.outline_color = outline_color;
	this.fill_color = fill_color;
};
		
Shape.prototype.selected = false;
Shape.prototype.setSelected = function(value) { this.selected = value;};
Shape.prototype.isSelected = function() { return this.selected;};

Shape.prototype.cloneShape = function () {
	var shape = new Shape(this.canvas, this.x1, this.y1, this.outline_width, this.outline_color, this.fill_color);
	shape.extend(this.x2, this.y2);
	return shape;
}

Shape.prototype.extend = function (x2, y2) {
    this.x2 = x2;
	this.y2 = y2;
};

Shape.prototype.move = function (distance_x, distance_y) {
	this.x1 += distance_x
    this.x2 += distance_x;
	this.y1 += distance_y;
	this.y2 += distance_y;
};

Shape.prototype.resize = function (val) {
	var dx = Math.abs(this.x2 - this.x1);
	var sign_x = 1, sign_y = 1;
	
	if (this.x2 < this.x1) // adjust resize value based on dx 
		sign_x = -1;
	
	if (this.y2 < this.y1) // adjust resize value based on dy
		sign_y = -1;
	
	if (dx == 0) {
		this.x2 += val;
	} else {
		var m = (this.y2 - this.y1) / (this.x2 - this.x1);
		var b = this.y1 - m*this.x1; 
		
		// resize according to the slope 
		if ( Math.abs(m) < 1) {
			this.x2 += sign_x * val;
			this.y2 = (this.x2 * m) + b;
		} else {
			this.y2 += sign_y * val;
			this.x2 = (this.y2 - b) / m;
		}
	}
};

Shape.prototype.updateLineWidth = function() {
	this.outline_width = $("#line_width").val();
	drawShapes();
};

Shape.prototype.updateLineColor = function() {
	this.outline_color = $("#line_color").spectrum("get").toHexString();;
	drawShapes();
};

Shape.prototype.updateFillColor = function() {
	this.fill_color = $("#fill_color").spectrum("get").toHexString();;
	drawShapes();
};
/***************************** Line *****************************************/
function Line(canvas, x1, y1, outline_width, outline_color) {
	Shape.call(this, canvas, x1, y1, outline_width, outline_color, null);
};

Line.prototype = new Shape(); // clone(Shape.prototype);
Line.prototype.constructor = Line;

Line.prototype.draw = function () {
    // Draw the line.
	this.context.beginPath();
    this.context.lineWidth = this.outline_width;
	this.context.strokeStyle = this.outline_color;
	
	this.context.moveTo(this.x1,this.y1);
	this.context.lineTo(this.x2,this.y2);
	this.context.stroke();
};

Line.prototype.testHit = function(testX,testY) {
	var dx = Math.abs(this.x2 - this.x1);
	
	if (dx == 0) {
		if (this.x1 + 5 > testX && this.x1 - 5 < testX)	
			return true;
	} else {
		var m = (this.y2 - this.y1) / (this.x2 - this.x1);
		var b = this.y1 - m*this.x1; 
		
		if ( Math.abs(m) < 1) {
			var y = m*testX + b;
			if (y + 5 > testY && y - 5 < testY)	
				return true;
		} else {
			var x = (testY - b) / m;
			if (x + 5 > testX && x - 5 < testX)	
				return true;
		}
	}
	
	return false;
};

/***************************** Triangle *****************************************/
function Triangle(canvas, x1, y1, outline_width, outline_color, fill_color) {
	Shape.call(this, canvas, x1, y1, outline_width, outline_color, fill_color);
};

Triangle.prototype = new Shape(); // clone(Shape.prototype);
Triangle.prototype.constructor = Triangle;

Triangle.prototype.draw = function () {
    // Draw the line.
	this.context.beginPath();
    this.context.lineWidth = this.outline_width;
	this.context.strokeStyle = this.outline_color;
	this.context.fillStyle   = this.fill_color;
	
	this.context.moveTo(this.x1,this.y1);
	this.context.lineTo(this.x2,this.y2);
	this.context.lineTo(this.x1,this.y2);
	this.context.lineTo(this.x1,this.y1);
	this.context.fill();
	this.context.stroke();
};

Triangle.prototype.testHit = function(testX,testY) {
	if ((testX < this.x1 && testX < this.x2) ||
		(testX > this.x1 && testX > this.x2))
		return false;
	
	if ((testY < this.y1 && testY < this.y2) ||
		(testY > this.y1 && testY > this.y2))
		return false;
	
	return true;
};

/***************************** Rectangle *****************************************/
function Rectangle(canvas, x1, y1, outline_width, outline_color, fill_color) {
	Shape.call(this, canvas, x1, y1, outline_width, outline_color, fill_color);
};

Rectangle.prototype = new Shape(); // clone(Shape.prototype);
Rectangle.prototype.constructor = Rectangle;

Rectangle.prototype.draw = function () {
    // Draw the line.
	this.context.beginPath();
    this.context.lineWidth = this.outline_width;
	this.context.strokeStyle = this.outline_color;
	this.context.fillStyle   = this.fill_color;
	
	this.context.rect(this.x1,this.y1,this.x2 - this.x1,this.y2 - this.y1);
	this.context.fill();
	this.context.stroke();
};

Rectangle.prototype.testHit = function(testX,testY) {
	if ((testX < this.x1 && testX < this.x2) ||
		(testX > this.x1 && testX > this.x2))
		return false;
	
	if ((testY < this.y1 && testY < this.y2) ||
		(testY > this.y1 && testY > this.y2))
		return false;
	
	return true;
};

/*************************** Other Functions ************************************/
var currentShape;  // shape being drawn
var selectedShape; // previously selected shape
var copiedShape;
var isDrawing = false;
var isMoving = false;
var lastClickX;
var lastClickY;
var toolSelected = 0; // 0 = Selection Tool, 1 = Line Tool, 2 = Triangle Tool, 3 = Rectangle Tool

function increaseSize() {
	if (selectedShape) {
		selectedShape.resize(5);
		drawShapes();
	}
}

function decreaseSize() {
	if (selectedShape) {
		selectedShape.resize(-5);
		drawShapes();
	}
}

function copyShape() {
	if (selectedShape)
		copiedShape = selectedShape;
}

function pasteShape() {
	if (copiedShape) {
		var shape = $.extend(true, {}, copiedShape);
		shapes.push(shape);
		drawShapes();
	}
}

function eraseShape() {
	if (shapes[shapes.length-1].isSelected()) {
		shapes.pop();
		drawShapes();
	}
}

function clearCanvas() {
	shapes = []; // Remove all the circles.  
	drawShapes(); // Update the display.
}

function drawShapes() {
  // Clear the canvas.
  context.clearRect(0, 0, canvas.width, canvas.height);

  // Go through all the shapes.
  for(var i=0; i<shapes.length; i++) {
    var shape = shapes[i];
    shape.draw();
  }
}

// This array hold all the circles on the canvas.
var shapes = [];

var canvas;
var context;

window.onload = function() {
  canvas = document.getElementById("canvas");
  context = canvas.getContext("2d");

  canvas.onmousedown = canvasOnMouseDown;
  canvas.onmousemove = canvasOnMouseMove;
  canvas.onmouseup = canvasOnMouseUp;
};

/*************************** Mouse Events ************************************/
function canvasOnMouseDown(e) {
  // Get the canvas click coordinates.
  var x = e.pageX - canvas.offsetLeft;
  var y = e.pageY - canvas.offsetTop;

  if (toolSelected == 0) {
	  // Look for the clicked shape.
	  for(var i=shapes.length-1; i>=0; i--) {
	    if (shapes[i].testHit(x,y)) {
	    	if (selectedShape) {
	    		selectedShape.setSelected(false);
	    		selectedShape = null;
	    	}
	    	shapes[i].setSelected(true);
	    	selectedShape = shapes[i];
	    	
	    	shapes.splice(i,1);
	    	shapes.push(selectedShape);

	    	drawShapes();
	    	isMoving = true;
	    	lastClickX = x;
	    	lastClickY = y;
	    	return;
	    }
	  }
	  return;
  }
  
	var line_width = $("#line_width").val();
	var line_color = $("#line_color").spectrum("get").toHexString();
	var fill_color = $("#fill_color").spectrum("get").toHexString();
  
  if (toolSelected == 1)
	  currentShape = new Line(canvas, x, y, line_width, line_color);
  else if (toolSelected == 2)
	  currentShape = new Triangle(canvas, x, y, line_width, line_color, fill_color);
  else if (toolSelected == 3)
	  currentShape = new Rectangle(canvas, x, y, line_width, line_color, fill_color);
  
  currentShape.draw();
  isDrawing = true;
}

function canvasOnMouseMove(e) {
	if (isDrawing == false && isMoving == false)
		return;
	
	// Get the canvas click coordinates.
	var x = e.pageX - canvas.offsetLeft;
	var y = e.pageY - canvas.offsetTop;
	
	if (isDrawing == true) {  
		drawShapes();
		currentShape.extend(x,y); 
		currentShape.draw();
	} else if (isMoving == true && selectedShape) {
		selectedShape.move(x - lastClickX,y - lastClickY);
		drawShapes();
		
    	lastClickX = x;
    	lastClickY = y;
	}
}

function canvasOnMouseUp(e) {
	if (isDrawing) {
		shapes.push(currentShape);
		
		if (selectedShape) {
			selectedShape.setSelected(false);
			selectedShape = null;
		}
	}
	
	isDrawing = false;
	isMoving = false;
}

function selectTool(n) {
	toolSelected = n;
}

$(document).ready(function() {
	$("#line_color").spectrum();
	$("#fill_color").spectrum();
	
	$("#line_width").change(function() { 
		if (selectedShape)
			selectedShape.updateLineWidth();
	});
	
	$("#fill_color").change(function() { 
		if (selectedShape)
			selectedShape.updateFillColor();
	});
	
	$("#line_color").change(function() { 
		if (selectedShape)
			selectedShape.updateLineColor();
	});
});
