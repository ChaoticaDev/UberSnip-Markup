# UberSnip-Markup
Harness the power of PHP using HTML Markup

# Overview
  UberSnipMarkup simply put, is a lexer; HTML markup is parsed, converted to PHP, and then executed. You can harness the power of PHP using pure Markup code.
  
  
##  Usage
### Available Tags

    <array /> : Attributes name-set, name, value
    <function></function> : Attributes name, params
    <var /> : Attributes name, value
    <repeat></repeat> : Attributes condition
    <call /> : Attributes function, params
    
    
### Array
      The array tag allows you to create an array to store data. <array /> is the equivelant of array() in php.
      
####  Attribute 'name-set' <array name-set="name_set_name" />
        Adding the 'name-set' attribute creates a multi-dimension array of arrays provided in the 'name' tag
        
####  Attribute 'name' <array name="$array_name, $array_name_2" />
        Adding the 'name' attribute will create an array with the given name. 
        Separate by comma to define multiple arrays.   
        $variable should be proceeded by $.
        
####  Attribute 'value' <array value="'array value', 'array value 2'" />
        Adding the 'value' assigns a value to the array at the given index.
        You may assign a $variable, or 'text wrapped in single quotes'
      
  
      
