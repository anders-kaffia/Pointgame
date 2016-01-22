--SQL QUERIES FOR POINTGAME

--TOTAL SCORE FROM CURRENT USER:

SELECT SUM(score)
FROM donelistitem, todolist, user
WHERE donelistitem.todolist_id = todolist.id
AND todolist.user_id = user.id
AND user.id = ".$id."


--TOTAL SCORE FROM CURRENT LIST:

SELECT SUM(score)
FROM donelistitem, todolist
WHERE donelistitem.todolist_id = todolist.id
AND todolist.id = ".$id."


--TOTAL SCORE, FROM UNFINISHED LISTITEMS, FROM CURRENT LIST:

SELECT SUM(score)
FROM listitem, todolist
WHERE listitem.todolist_id = todolist.id
AND todolist.id = ".$id."

--TOTAL NUMBER OF LISTS FROM CURRENT USER:

SELECT COUNT(todolist.id)
FROM todolist, user
WHERE todolist.user_id = user.id
AND user.id = 6

--TOTAL NUMBER OF POINTS FROM LIST TYPE:
SELECT SUM(score)
FROM donelistitem, todolist, user
WHERE donelistitem.todolist_id = todolist.id
AND todolist.type = 'week'
AND todolist.user_id = user.id
AND user.id = 10

--TOTAL NUMBER OF UNFINISHED LISTITEMS: 

SELECT COUNT(listitem.id)
FROM listitem, todolist
WHERE listitem.todolist_id = todolist.id
AND todolist.id = ".$id."